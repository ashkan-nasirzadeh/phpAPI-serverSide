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
use PhpAPI\Log as Log;
class SearchRowsPagination extends CRUD {
    private $serverName;
    private $uName;
    private $pass;
    private $db;
    private $table;
    private $where;
    private $like;
    private $exceptionColumns;
    private $page;
    private $count;
    private $settings;
    public function __construct ($serverName, $uName, $pass, $db, $table, $where, $like, $exceptionColumns,$page, $count, $settings) {
        $this->serverName = $serverName;
        $this->uName = $uName;
        $this->pass = $pass;
        $this->db = $db;
        $this->table = $table;
        $this->where = $where;
        $this->like = $like;
        $this->exceptionColumns = $exceptionColumns;
        $this->page = $page;
        $this->count = $count;
        $this->settings = $settings;
    }
    public function readRows_pagination() {
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
                        'returnedRowsCount' => $resultsCount,
                        'totalPagesCount' => $this->getTotalPages_return()
                    ],
                    'output' => $resultsJson
                ],
                'settings' => $this->settings
            ];
            return $this->finalizeOutput($output);
        } catch (PDOException $PDOException) {
            echo $PDOException;
        }
    }
    public function makeSqlSentence () {
        //        $sql = "SELECT * FROM users WHERE fName LIKE '%oh%' AND sName='farhadi'";
        $conditions = [];
        $table = $this->table;
        $where = $this->where;
        $like = $this->like;
        $sql = 'SELECT * FROM '.$table;
        if (empty($where) && empty($like)) return $sql;
        $sql .= ' WHERE ';
        foreach ($like as $key => $val) {
            $conditions[] = $key . ' LIKE ' . "'%$val%'";
        }
        foreach ($where as $key => $val) {
            $conditions[] = $key.'='."'$val'";
        }
        $last_memberOf_conditions = end($conditions);
        foreach ($conditions as $condition) {
            if ($last_memberOf_conditions != $condition) $sql .= $condition.' AND ';
            else $sql .= $condition;
        }
        /**
         * ┌───────────────────┐
         * │ debug zone -start │
         * └───────────────────┘
         */
        $log = new Log();
        $log->warning($sql.' '.$this->getLimit());
        /**
         * ┌─────────────────┐
         * │ debug zone -end │
         * └─────────────────┘
         */
        return $sql.' '.$this->getLimit();
    }
    public function getTotalRows_echo() {
        $table = $this->table;
        $where = $this->where;
        $sql = 'SELECT COUNT(*) FROM ' . $table;
        if (!empty($where)) {
            $sql .= ' WHERE ';
            $lastKey_of_where = array_key_last($where);
            foreach ($where as $columnName => $columnValue) {
                if ($columnName != $lastKey_of_where) $sql .= $columnName.'=\''.$columnValue.'\' AND ';
                else $sql .= $columnName.'=\''.$columnValue.'\'';
            }
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rowsCount = $stmt->fetchColumn('0');
        echo $rowsCount;
    }
    public function getTotalRows_return() {
        $conditions = [];
        $table = $this->table;
        $where = $this->where;
        $like = $this->like;
        $sql = 'SELECT COUNT(*) FROM '.$table;
        if (empty($where) && empty($like)) return $sql;
        $sql .= ' WHERE ';
        foreach ($like as $key => $val) {
            $conditions[] = $key . ' LIKE ' . "'%$val%'";
        }
        foreach ($where as $key => $val) {
            $conditions[] = $key.'='."'$val'";
        }
        $last_memberOf_conditions = end($conditions);
        foreach ($conditions as $condition) {
            if ($last_memberOf_conditions != $condition) $sql .= $condition.' AND ';
            else $sql .= $condition;
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rowsCount = $stmt->fetchColumn('0');
        return $rowsCount;
    }
    public function getTotalPages_return() {
        $table = $this->table;
        $where = $this->where;
        $sql = 'SELECT COUNT(*) FROM ' . $table;
        if (!empty($where)) {
            $sql .= ' WHERE ';
            $lastKey_of_where = array_key_last($where);
            foreach ($where as $columnName => $columnValue) {
                if ($columnName != $lastKey_of_where) $sql .= $columnName.'=\''.$columnValue.'\' AND ';
                else $sql .= $columnName.'=\''.$columnValue.'\'';
            }
        }
        $conn = $this->makeConn($this->serverName, $this->uName, $this->pass, $this->db);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rowsCount = $stmt->fetchColumn('0');
        $pagesCount = ceil($rowsCount/$this->count);
        return $pagesCount;
    }
    public function getLimit () {
        $page = $this->page;
        $count = $this->count;
        $startingNumber = ($page-1) * $count;
        $totalRows = $this->getTotalRows_return();
        if ($startingNumber >= $totalRows) {
            $startingNumber = 0;
        }
        $limitSentence = 'LIMIT '.$startingNumber.','.$count;
        return $limitSentence;
    }
}
