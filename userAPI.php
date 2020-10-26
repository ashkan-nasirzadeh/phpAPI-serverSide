<?php
namespace PhpAPI;
use PhpAPI\AddRow;
use PhpAPI\DeleteRows;
use PhpAPI\ReadRows;
use PhpAPI\UpdateRows;
use PhpAPI\CheckColumnHashVerified;
use PhpAPI\SearchRows;
class UserAPI extends \PhpAPI\Router {
    private $extra;
    private $jwtData = [];
    public function __construct ($task, $extra) {
        $jwt = $this->returnJwtIfProvided();
        if ($jwt) $this->jwtData = $this->returnJwtDataIfJwtIsValid($jwt);
        $this->extra = $extra;
        switch ($task):
            case 'addRow': $this->user_addRow(); break;
            case 'readRows': $this->user_readRows(); break;
            case 'updateRows': $this->user_updateRows(); break;
            case 'deleteRows': $this->user_deleteRows(); break;
            case 'uploadPhoto': $this->user_uploadPhoto(); break;
            case 'checkColumnHashVerified': $this->user_checkColumnHashVerified(); break;
            case 'searchRows': $this->user_searchRows(); break;
        endswitch;
    }
    private function user_addRow () {
        require_once 'AddRow.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $dataToAdd = [
            'fName' => 'john',
            'sName' => 'farhadi',
            'pass' => '1234'
        ];
        $columnsToHash = [
            'pass' => 'PASSWORD_DEFAULT'
        ];
        $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
        $addRow = new AddRow($serverName, $uName, $pass, $db, $table, $dataToAdd, $columnsToHash, $settings);
        $addRow->addRow();
    }
    private function user_readRows () {
        require_once 'ReadRows.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $where = [
            'sName' => 'tt',
        ];
        $exceptionColumns = ['sName'];
        $settings = ['needJwtValidation' => false, 'addJwt' => ['id' => 1], 'echoOrReturn' => 'echo'];
        $readRows = new ReadRows($serverName, $uName, $pass, $db, $table, $where, $exceptionColumns, $settings);
        $readRows->readRows();
    }
    private function user_searchRows () {
        require_once 'SearchRows.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $where = [
            'sName' => 'farhadi',
            'ID' => 142
        ];
        $like = [
            'fName' => 'oh'
        ];
        $exceptionColumns = [];
        $settings = ['needJwtValidation' => false, 'addJwt' => ['id' => 1], 'echoOrReturn' => 'echo'];
        $searchRows = new SearchRows($serverName, $uName, $pass, $db, $table, $where, $like, $exceptionColumns, $settings);
        $searchRows->searchRows();
    }
    private function user_updateRows () {
        require_once 'UpdateRows.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $where = [
            'fName' => 'john'
        ];
        $set = [
            'sName' => 'tt',
            'pass' => '1234'
        ];
        $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
        $updateRows = new updateRows($serverName, $uName, $pass, $db, $table, $where, $set, $settings);
        $updateRows->updateRows();
    }
    private function user_deleteRows () {
        require_once 'DeleteRows.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $where = [
            'fName' => 'john'
        ];
        $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
        $deleteRows = new DeleteRows($serverName, $uName, $pass, $db, $table, $where, $settings);
        $deleteRows->deleteRows();
    }
    private function user_uploadPhoto () {
        require_once 'UploadPhoto.php';
        $target_dir = "uploads" . DIRECTORY_SEPARATOR;
        $fileName = 'kia';
        $maxFileSize = '1000000'; //1 MegaByte
        $settings = ['needJwtValidation' => false, 'addJwt' => true, 'echoOrReturn' => 'echo'];
        $uploadPhoto = new UploadPhoto($target_dir, $fileName, $maxFileSize, $settings);
        $uploadPhoto->uploadPhoto();
    }
    private function user_checkColumnHashVerified () {
        require_once 'CheckColumnHashVerified.php';
        $serverName = "localhost";
        $uName = "root";
        $pass = "";
        $db = 'phpapi';
        $table = 'users';
        $where = [
            'fName' => 'john',
            'sName' => 'b'
        ];
        $hashedColumn = 'pass';
        $extra = $this->extra;
        $stringFromClientToVerify = $extra['stringFromClientToVerify'];
        $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
        $CheckColumnHashVerified = new CheckColumnHashVerified($serverName, $uName, $pass, $db, $table, $where, $hashedColumn, $stringFromClientToVerify, $settings);
        $CheckColumnHashVerified->CheckColumnHashVerified();
    }
}