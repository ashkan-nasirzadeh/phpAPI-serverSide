<?php
namespace PhpAPI;
use PDO;
//TODO check all json_encode functions to have JSON_UNESCAPED_SLASHES flag
require_once 'log.php';

class CRUD extends Router
{
    public function hashedColumnValuePairs($ColumnValuePairs, $columnsToHash)
    {
        $hashedColumnValuePairs = [];
        foreach ($ColumnValuePairs as $columnName => $columnValue) {
            if (array_key_exists($columnName, $columnsToHash)) {
                $valueToHash = $columnValue;
                $hashType = $columnsToHash[$columnName];
                if ($hashType == 'PASSWORD_DEFAULT') $columnValue = password_hash($valueToHash, PASSWORD_DEFAULT);
                else if ($hashType == 'PASSWORD_BCRYPT') $columnValue = password_hash($valueToHash, PASSWORD_BCRYPT);
                else if ($hashType == 'PASSWORD_ARGON2I') $columnValue = password_hash($valueToHash, PASSWORD_ARGON2I);
                else if ($hashType == 'PASSWORD_ARGON2ID') $columnValue = password_hash($valueToHash, PASSWORD_ARGON2ID);
                else $columnValue = false;
                if (!$columnValue) return false;
            }
            $hashedColumnValuePairs[$columnName] = $columnValue;
        }
        return $hashedColumnValuePairs;
    }
    public function unhashedColumnValuePairs($ColumnValuePairs, $columnsToHash)
    {
        $hashedColumnValuePairs = [];
//        foreach ($ColumnValuePairs as $columnName => $columnValue) {
//            if (array_key_exists($columnName, $columnsToHash)) {
//                $valueToHash = $columnValue;
//                $hashType = $columnsToHash[$columnName];
//                if ($hashType == 'PASSWORD_DEFAULT') $columnValue = password_hash($valueToHash, PASSWORD_DEFAULT);
//                else if ($hashType == 'PASSWORD_BCRYPT') $columnValue = password_hash($valueToHash, PASSWORD_BCRYPT);
//                else if ($hashType == 'PASSWORD_ARGON2I') $columnValue = password_hash($valueToHash, PASSWORD_ARGON2I);
//                else if ($hashType == 'PASSWORD_ARGON2ID') $columnValue = password_hash($valueToHash, PASSWORD_ARGON2ID);
//                else $columnValue = false;
//                if (!$columnValue) return false;
//            }
//            $hashedColumnValuePairs[$columnName] = $columnValue;
//        }
//        return $hashedColumnValuePairs;
    }
    public function makeConn($serverName, $uName, $pass, $db)
    {
        $conn = new PDO("mysql:host=$serverName;dbname=$db;charset=utf8", $uName, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}



