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
//        $extra = $this->extra;
//        if (
//            !isset($extra['id']) ||
//            empty($extra['id'])
//        ) {
//            echo 'Error: not enough args or empty';
//            return;
//        }
//        $ID = $extra['id'];
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
    private function searchProducts_pagination () {
      $extra = $this->extra;
      if (
          !isset($extra['like']) ||
          !isset($extra['page']) ||
          !isset($extra['count']) ||
          empty($extra['like']) ||
          empty($extra['page']) ||
          empty($extra['count'])

      ) {
          echo 'Error: not enough args or empty';
          return;
      }
      $like = $extra['like'];
      $page = $extra['page'];
      $count = $extra['count'];
      require_once 'SearchRowsPagination.php';
      $serverName = $this->serverName;
      $uName = $this->uName;
      $pass = $this->pass;
      $db = $this->db;
      $table = 'products';
      $where = [];
      $like = [
          'title' => "$like"
      ];
      $exceptionColumns = [];
      $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
      $readRows_pagination = new SearchRowsPagination($serverName, $uName, $pass, $db, $table, $where, $like, $exceptionColumns, $page, $count, $settings);
      $readRows_pagination->readRows_pagination();
    }
    private function getProducts () {
      $extra = $this->extra;
      if (
          !isset($extra['page']) ||
          !isset($extra['count']) ||
          !isset($extra['subGroup']) ||
          empty($extra['page']) ||
          empty($extra['count']) ||
          empty($extra['subGroup'])
      ) {
          echo 'Error: not enough args or empty';
          return;
      }
      require_once 'ReadRows_pagination.php';
      $serverName = $this->serverName;
      $uName = $this->uName;
      $pass = $this->pass;
      $db = $this->db;
      $table = 'products';
      $exceptionColumns = [];
      $page = $extra['page'];
      $count = $extra['count'];
      $subGroup = $extra['subGroup'];
      if ($subGroup == 'all') {
          $where = [];
      } else {
          $where = [
              'subGroup' => "$subGroup"
          ];
      }
      $settings = ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo'];
      $readRows_pagination = new ReadRows_pagination($serverName, $uName, $pass, $db, $table, $where, $exceptionColumns, $page, $count, $settings);
      $readRows_pagination->readRows_pagination();
//        $readRows_pagination->getTotalPagesCount();
//        $readRows_pagination->getLimit();
//        $readRows_pagination->getTotalPages_return();
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
//        $extra = $this->extra;
//        $extraDecoded = json_decode($extra, true);
//        if (
//            !isset($extraDecoded['id']) ||
//            empty($extraDecoded['id'])
//        ) {
//            echo 'Error: not enough args or empty';
//            return;
//        }
//        $ID = $extraDecoded['id'];
        require_once 'UploadPhoto.php';
        $target_dir = "uploads" . DIRECTORY_SEPARATOR;
        $fileName = 'kia';
        $maxFileSize = '1000000'; //1 MegaByte
        $settings = ['needJwtValidation' => true, 'addJwt' => ['ID' => 1], 'echoOrReturn' => 'echo', 'overwrite' => true];
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
    private function getProductsForAdmin () {
        $extra = $this->extra;
        if (
            !isset($extra['page']) ||
            !isset($extra['count']) ||
            empty($extra['page']) ||
            empty($extra['count'])
        ) {
            echo 'Error: not enough args or empty';
            return;
        }
        require_once 'ReadRows_pagination.php';
        $serverName = $this->serverName;
        $uName = $this->uName;
        $pass = $this->pass;
        $db = $this->db;
        $table = 'products';
        $exceptionColumns = [];
        $where = [];
        $page = $extra['page'];
        $count = $extra['count'];
        $settings = ['needJwtValidation' => false, 'addJwt' => ['ID' => 1], 'echoOrReturn' => 'echo'];
        $readRows_pagination = new ReadRows_pagination($serverName, $uName, $pass, $db, $table, $where, $exceptionColumns, $page, $count, $settings);
        $readRows_pagination->readRows_pagination();
//        $readRows_pagination->getTotalPagesCount();
//        $readRows_pagination->getLimit();
//        $readRows_pagination->getTotalPages_return();
    }
    private function uploadProductPhoto () {
        $extra = $this->extra;
        $extraDecoded = json_decode($extra, true);
        if (
            !isset($extraDecoded['id']) ||
            !isset($extraDecoded['picNum']) ||
            empty($extraDecoded['id']) ||
            empty($extraDecoded['picNum'])
        ) {
            echo 'Error: not enough args or empty';
            return;
        }
        $ID = $extraDecoded['id'];
        $picNum = $extraDecoded['picNum'];

        require_once 'UploadPhoto.php';
//        $target_dir = "C:/xampp/htdocs/dashboard/payelcd_ir shaaboon/uploads" . DIRECTORY_SEPARATOR;
        $target_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
        $fileName = 'productID_'.$ID.'_productPicNum_'.$picNum;
        $maxFileSize = '1000000'; //1 MegaByte
        $settings = ['needJwtValidation' => true, 'addJwt' => false, 'echoOrReturn' => 'return', 'overwrite' => true];
        $uploadPhoto = new UploadPhoto($target_dir, $fileName, $maxFileSize, $settings);
        $result = $uploadPhoto->uploadPhoto();
        $doesPhotoUploaded = $result['status']['sCode'];
        if (!$doesPhotoUploaded) {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 100,
                        'sMessage' => "file did not upload"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            return $this->finalizeOutput($output);
        }
        $imageFormat = $result['status']['format'];
        require_once 'UpdateRows.php';
        $serverName = $this->serverName;
        $uName = $this->uName;
        $pass = $this->pass;
        $db = $this->db;
        $table = 'products';
        $where = [
            'ID' => "$ID"
        ];
        require_once 'config.php';
        $columnForAssociatedPic = 'pic' . $picNum;
        $set = [
            "$columnForAssociatedPic" => ROOT . "uploads/products/$fileName.$imageFormat"
        ];
        $settings = ['needJwtValidation' => true, 'addJwt' => ['ID' => 1], 'echoOrReturn' => 'echo'];
        $updateRows = new updateRows($serverName, $uName, $pass, $db, $table, $where, $set, $settings);
        $updateRows->updateRows();
    }
}
