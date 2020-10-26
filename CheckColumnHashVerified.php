<?php
//$serverName = "localhost";
//$uName = "root";
//$pass = "";
//$db = 'phpapi';
//$table = 'users';
//$where = [
//    'fName' => 'john',
//];
//$hashedColumn = 'pass';
//$stringFromClientToVerify = '1234';
namespace PhpAPI;
require_once 'CRUD.php';
require_once 'log.php';
use PhpAPI\CRUD as CRUD;
use PhpAPI\Log as Log;
use PDO;
class CheckColumnHashVerified extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $where;
    private $hashedColumn;
    private $stringFromClientToVerify;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $where, $hashedColumn, $stringFromClientToVerify, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->where = $where;
        $this->hashedColumn = $hashedColumn;
        $this->stringFromClientToVerify = $stringFromClientToVerify;
        $this->settings = $settings;
    }
    public function makeSqlSentence () {
        $hashedColumn = $this->hashedColumn;
        $table = $this->table;
        $where = $this->where;

        $sql = 'SELECT ';
        $sql .= $hashedColumn;
        $sql .= ' FROM ';
        $sql .= $table . ' WHERE ';
        $lastKey_of_where = array_key_last($where);
        foreach ($where as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_where) $sql .= $columnName . '=\'' . $columnValue . '\' AND ';
            else $sql .= $columnName . '=\'' . $columnValue . '\'';
        }
        /**
         * ┌───────────────────┐
         * │ debug zone -start │
         * └───────────────────┘
         */
        $log = new Log();
        $log->warning($sql);
        /**
         * ┌─────────────────┐
         * │ debug zone -end │
         * └─────────────────┘
         */
        return $sql;

    }
    public function CheckColumnHashVerified () {
        $settings = $this->settings;
        $hashedColumn = $this->hashedColumn;
        $stringFromClientToVerify = $this->stringFromClientToVerify;
        if (isset($settings['needJwtValidation']) && $settings['needJwtValidation']) {
            if(!$this->checkJwt()) return;
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $sql = $this->makeSqlSentence();
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // if user required column $hashedColumn doesn't exists this block throw an error and goes to catch block
            $rawRowsToReturn = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rawRowsToReturn) != 1) {
                $output = [
                    'output' => [
                        'success' => false,
                        'status' => [
                            'sCode' => 2,
                            'sMessage' => "more than or less than one row returned by your when conditions"
                        ],
                        'output' => []
                    ],
                    'settings' => $this->settings
                ];
                $this->finalizeOutput($output);
                return;
            } else {
                $doesPassVerified = password_verify($stringFromClientToVerify, $rawRowsToReturn[0][$hashedColumn]);
            }
            $output = [
                'output' => [
                    'success' => true,
                    'status' => [
                        'sCode' => $doesPassVerified ? 1 : 2,
                        'sMessage' => $doesPassVerified ? "stringFromClientToVerify verified" : "stringFromClientToVerify doesn't verified"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);

        } catch (PDOException $PDOException) {
            echo $PDOException;
        }
    }
}



