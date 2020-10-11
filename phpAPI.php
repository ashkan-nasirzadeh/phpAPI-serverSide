<?php
namespace phpAPI;
class PhpAPI {
    private $API_secretKey = 'kia1995ns';
    private $user_secretKey;
    public function __construct($secretKey) {
        $this->user_secretKey = $secretKey;
    }
    public function __call($method,$arguments) {
        if(method_exists($this, $method)) {
            if ($this->isSecretKeyTrue()) {
                return call_user_func_array(array($this,$method),$arguments);
            } else {
                echo 'secret key is wrong';
            }
        }
    }
    public function isSecretKeyTrue () {
        if ($this->user_secretKey == $this->API_secretKey) return true;
        else return false;
    }
    public function getRequestDataBody () {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            return [];
        }
        $data = json_decode($body, true);
        if (json_last_error()) {
            trigger_error(json_last_error_msg());
            return [];
        }
        return $data;
    }
    public function echoOutput ($output) {
        $output = json_encode($output);
        echo $output;
    }
    public function hash ($valueToHash, $hashType) {
        $hashes = hash_algos();
        $isHashTypeValid = in_array($hashType, $hashes);
        if (!$isHashTypeValid) {
            return false;
        }
        $output = hash($hashType, $valueToHash);
        return $output;
    }
    public function hashedColumnValuePairs ($ColumnValuePairs, $columnsToHash) {
        $hashedColumnValuePairs = [];
        foreach ($ColumnValuePairs as $columnName => $columnValue) {
            if (array_key_exists($columnName, $columnsToHash)) {
                $valueToHash = $columnValue;
                $hashType = $columnsToHash[$columnName];
                $columnValue = $this->hash($valueToHash, $hashType);
                if (!$columnValue) return false;
            }
            $hashedColumnValuePairs[$columnName] = $columnValue;
        }
        return $hashedColumnValuePairs;
    }
    public function unhash ($hashedValue, $hashType) {

    }
    private function makeConn ($serverName, $uName, $pass, $db) {
        $conn = new PDO("mysql:host=$serverName;dbname=$db;charset=utf8", $uName, $pass);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
    private function addRow() {
        /** @var TYPE_NAME $serverName */
        /** @var TYPE_NAME $uName */
        /** @var TYPE_NAME $pass */
        /** @var TYPE_NAME $db */
        /** @var TYPE_NAME $dataToAdd */
        /** @var TYPE_NAME $table */
        /** @var TYPE_NAME $columnsToHash */
        global $receivedBody;
        extract($receivedBody);
        $conn = $this->makeConn($serverName, $uName, $pass, $db);
//        extract($dataToAdd, EXTR_PREFIX_ALL, 'userColumn_'); // for example now we have $userColumn__fName
        $sql = '';
        $sql .= 'INSERT INTO ' . $table . ' (';
        $lastKey_of_dataToAdd = array_key_last($dataToAdd);
        foreach ($dataToAdd as $columnName => $columnValue) {
            if ($columnName != $lastKey_of_dataToAdd) $sql .= $columnName . ', ';
            else $sql .= $columnName . ') VALUES (';
        }
        foreach ($dataToAdd as $columnName => $columnValue) {
            if (!$this->hashedColumnValuePairs($dataToAdd, $columnsToHash)) {
                    $output = [
                        'success' => false,
                        'status' => [
                            'sCode' => 2,
                            'sMessage' => 'wrong hash type value'
                        ],
                        'output' => []
                    ];
                    $this->echoOutput($output);
                    return;
            }
            if ($columnName != $lastKey_of_dataToAdd) $sql .= '\'' . $columnValue . '\'' . ', ';
            else $sql .= '\'' . $columnValue . '\'' . ')';
        }
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $output = [
                'success' => true,
                'status' => [
                    'sCode' => 1,
                    'sMessage' => 'new record added'
                ],
                'output' => []
            ];
            $this->echoOutput($output);

        } catch (PDOException $PDOExceptionError) {
            echo $sql . "<br>" . $PDOExceptionError->getMessage();
        }
    }
}

$receivedBody = PhpAPI::getRequestDataBody();
$secretKey = $receivedBody['secretKey'];
$task = $receivedBody['task'];
$API = new PhpAPI($secretKey);
$API->$task();
//PhpAPI::hash('1234', 'sha512/224');