<?php
//$serverName = "localhost";
//$uName = "root";
//$pass = "";
//$db = 'phpapi';
//$table = 'users';
//$dataToAdd = [
//    'fName' => 'john',
//    'sName' => 'farhadi',
//    'pass' => '1234'
//];
//$columnsToHash = [
//    'pass' => 'PASSWORD_DEFAULT'
//];
namespace PhpAPI;
require_once 'CRUD.php';
use PhpAPI\CRUD as CRUD;
class AddRow extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $dataToAdd;
    private $columnsToHash;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $dataToAdd, $columnsToHash, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->dataToAdd = $dataToAdd;
        $this->columnsToHash = $columnsToHash;
        $this->settings = $settings;
    }
    public function makeSqlSentence () {
        $table = $this->table;
        $dataToAdd = $this->dataToAdd;
        $columnsToHash = $this->columnsToHash;

        $sql = '';
        $sql .= 'INSERT INTO ' . $table . ' (';
        $lastKey_of_dataToAdd = array_key_last($dataToAdd);
        $hashedColumnValuePairs = $this->hashedColumnValuePairs($dataToAdd, $columnsToHash);
        if (!$hashedColumnValuePairs) {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 2,
                        'sMessage' => 'wrong hash type value'
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        }
        foreach ($hashedColumnValuePairs as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_dataToAdd) $sql .= $columnName . ', ';
            else $sql .= $columnName . ') VALUES (';
        }
        foreach ($hashedColumnValuePairs as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_dataToAdd) $sql .= '\'' . $columnValue . '\'' . ', ';
            else $sql .= '\'' . $columnValue . '\'' . ')';
        }
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
    public function addRow () {
        $settings = $this->settings;
        if (isset($settings['needJwtValidation']) && $settings['needJwtValidation']) {
            if(!$this->checkJwt()) return;
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $sql = $this->makeSqlSentence();
        if ($sql):
            try {
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $output = [
                    'output' => [
                        'success' => true,
                        'status' => [
                            'sCode' => 1,
                            'sMessage' => 'new record added'
                        ],
                        'output' => []
                    ],
                    'settings' => $this->settings
                ];
                $this->finalizeOutput($output);
            } catch (PDOException $PDOExceptionError) {
                echo $sql . "<br>" . $PDOExceptionError->getMessage();
            }
        endif;
    }
}