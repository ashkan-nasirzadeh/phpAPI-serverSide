<?php
//$serverName = "localhost";
//$uName = "root";
//$pass = "";
//$db = 'phpapi';
//$table = 'users';
//$where = [
//    'fName' => 'john'
//];
namespace PhpAPI;
require_once 'CRUD.php';
use PhpAPI\CRUD as CRUD;
class DeleteRows extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $where;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $where, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->where = $where;
        $this->where = $where;
        $this->settings = $settings;
    }
    public function deleteRows () {
        $settings = $this->settings;
        if (isset($settings['needJwtValidation']) && $settings['needJwtValidation']) {
            if(!$this->checkJwt()) return;
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $sql = $this->makeSqlSentence();
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $updatedRowsCount = $stmt->rowCount();
            $output = [
                'output' => [
                    'success' => true,
                    'status' => [
                        'sCode' => 1,
                        'sMessage' => "deleted $updatedRowsCount row successfully",
                        'deletedRowsCount' => "$updatedRowsCount"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $output = [

            ];
            $this->finalizeOutput($output);
        } catch (PDOException $PDOException) {
            echo $PDOException;
        }
    }
    public function makeSqlSentence () {
        //        $sql = "UPDATE Customers SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' WHERE CustomerID = 1;";
        $table = $this->table;
        $where = $this->where;
        $sql = 'DELETE FROM ';
        $sql .= $table;
        if (empty($where) && empty($like)) return $sql;
        $sql .= ' WHERE ';
        $lastKey_of_where = array_key_last($where);
        foreach ($where as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_where) $sql .= $columnName.'=\''.$columnValue.'\' AND ';
            else $sql .= $columnName.'=\''.$columnValue.'\'';
        }
        $sql .= ";";
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

