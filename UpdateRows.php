<?php
//$serverName = "localhost";
//$uName = "root";
//$pass = "";
//$db = 'phpapi';
//$table = 'users';
//$where = [
//    'fName' => 'john'
//];
//$set = [
//    'sName' => 'tt',
//    'pass' => '1234'
//];
namespace PhpAPI;
require_once 'CRUD.php';
use PhpAPI\CRUD as CRUD;
class UpdateRows extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $where;
    private $set;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $where, $set, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->where = $where;
        $this->set = $set;
        $this->settings = $settings;
    }
    public function updateRows () {
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

            ];
            $output = [
                'output' => [
                    'success' => true,
                    'status' => [
                        'sCode' => 1,
                        'sMessage' => "updated $updatedRowsCount row successfully",
                        'updatedRowsCount' => "$updatedRowsCount"
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
    public function makeSqlSentence () {
        // $sql = "UPDATE Customers SET ContactName = 'Alfred Schmidt', City= 'Frankfurt' WHERE CustomerID = 1;";
        $table = $this->table;
        $set = $this->set;
        $where = $this->where;
        $sql = 'UPDATE ';
        $sql .= $table . ' SET ';
        $lastKey_of_set = array_key_last($set);
        foreach ($set as $columnName => $columnNewValue) {
            if ($columnName != $lastKey_of_set) $sql .= $columnName.'=\''.$columnNewValue.'\' AND ';
            else $sql .= $columnName.'=\''.$columnNewValue.'\'';
        }

        if (empty($where) && empty($like)) return $sql;
        $sql .= ' WHERE ';

        $lastKey_of_where = array_key_last($where);
        foreach ($where as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_where) $sql .= $columnName.'=\''.$columnValue.'\' AND ';
            else $sql .= $columnName.'=\''.$columnValue.'\'';
        }
        $sql .= ";";
        return $sql;
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
    }
}

