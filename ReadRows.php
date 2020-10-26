<?php
//$serverName = "localhost";
//$uName = "root";
//$pass = "";
//$db = 'phpapi';
//$table = 'users';
//$where = [
//    'sName' => 'tt',
//];
//$exceptionColumns = ['sName'];
namespace PhpAPI;
require_once 'CRUD.php';
use PhpAPI\CRUD as CRUD;
use PDO;
class ReadRows extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $where;
    private $exceptionColumns;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $where, $exceptionColumns, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->where = $where;
        $this->exceptionColumns = $exceptionColumns;
        $this->settings = $settings;
    }
    public function readRows() {
        $settings = $this->settings;
        if (isset($settings['needJwtValidation']) && $settings['needJwtValidation']) {
            if(!$this->checkJwt()) return;
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $sql = $this->makeSqlSentence();
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rawRowsToReturn = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rawRowsToReturn as $rowKey => $rowValue) {
                foreach ($this->exceptionColumns as $columnToExcept) {
                    if (isset($rowValue[$columnToExcept])) {
                        unset($rawRowsToReturn[$rowKey][$columnToExcept]);
                    } else {
                        $output = [
                            'output' => [
                                'success' => false,
                                'status' => [
                                    'sCode' => 2,
                                    'sMessage' => "there is no $columnToExcept column name to exclude"
                                ],
                                'output' => []
                            ],
                            'settings' => $this->settings
                        ];
                        $this->finalizeOutput($output);
                        return;
                    }
                }
            }
            $resultsCount = count($rawRowsToReturn);
            $resultsJson = json_encode($rawRowsToReturn, JSON_UNESCAPED_SLASHES);
            $output = [
                'output' => [
                    'success' => true,
                    'status' => [
                        'sCode' => 1,
                        'sMessage' => "$resultsCount row returned",
                        'returnedRowsCount' => $resultsCount
                    ],
                    'output' => $resultsJson
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
        } catch (PDOException $PDOException) {
            echo $PDOException;
        }
    }
    public function makeSqlSentence () {
        //        $sql = "SELECT * FROM Customers WHERE Country='Mexico'";
        $table = $this->table;
        $where = $this->where;
        $sql = 'SELECT * FROM ';
        $sql .= $table;
        if (empty($where) && empty($like)) return $sql;
        $sql .= ' WHERE ';
        $lastKey_of_where = array_key_last($where);
        foreach ($where as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_where) $sql .= $columnName.'=\''.$columnValue.'\' AND ';
            else $sql .= $columnName.'=\''.$columnValue.'\'';
        }
        /**
         * ┌───────────────────┐
         * │ debug zone -start │
         * └───────────────────┘
         */
        //        global $log;
        //        $log->warning("$sql");
        /**
         * ┌─────────────────┐
         * │ debug zone -end │
         * └─────────────────┘
         */
        return $sql;
    }
}