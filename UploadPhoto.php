<?php
//$target_dir = "uploads" . DIRECTORY_SEPARATOR;
//$fileName = 'kia';
//$maxFileSize = '1000000'; //1 MegaByte
namespace PhpAPI;
require_once 'CRUD.php';
use PhpAPI\CRUD as CRUD;
/**
 * ┌───────────────────────────────────────────────────────┐
 * │ setup an error_handler for getimagesize notice -start │
 * └───────────────────────────────────────────────────────┘
 * @param $severity
 * @param $message
 * @param $filename
 * @param $lineno
 */
function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() && $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}
set_error_handler('PhpAPI\exceptions_error_handler');
/**
 * ┌─────────────────────────────────────────────────────┐
 * │ setup an error_handler for getimagesize notice -End │
 * └─────────────────────────────────────────────────────┘
 */
class UploadPhoto extends CRUD {
    private $target_dir;
    private $fileName;
    private $maxFileSize;
    private $settings;
    private $target_file;
    private $imageFileType;
    public $JWTff;
    public function __construct($target_dir, $fileName, $maxFileSize, $settings) {
        $this->target_dir = $target_dir;
        $this->fileName = $fileName;
        $this->maxFileSize = $maxFileSize;
        $this->settings = $settings;
    }
    public function isFileArrayOk () {
        $conditionsOBJ = [
            isset($_FILES['file']['name']),
            isset($_FILES['file']['size']),
            isset($_FILES['file']['tmp_name']),
            !empty($_FILES['file']['name']),
            !empty($_FILES['file']['size']),
            !empty($_FILES['file']['tmp_name'])
        ];
        $isAllConditionsOk = true;
        foreach ($conditionsOBJ as $condition) {
            $isAllConditionsOk = $condition && $isAllConditionsOk;
        }
        if ($isAllConditionsOk) {
            $target_dir = $this->target_dir;
            $fileName = $this->fileName;
            $this->imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
            $this->target_file = $target_dir . $fileName . '.' . $this->imageFileType;
            return true;
        }
        else {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 6,
                        'sMessage' => '$_FILES is not ok'
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        }
    }
    public function isActualImage () {
        try {
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            return true;
        } catch (Exception $e) {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 2,
                        'sMessage' => "this files is not a valid image"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        }
    }
    public function doesFileAlreadyExists () {
        $target_file = $this->target_file;
        if (file_exists($target_file)) {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 3,
                        'sMessage' => "a file with same path exists"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        } else {
            return true;
        }
    }
    public function isFileSizeOk () {
        $maxFileSize = $this->maxFileSize;
        if ($_FILES["file"]["size"] > $maxFileSize) { //file size in bytes
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 4,
                        'sMessage' => "file is very large"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        } else {
            return true;
        }
    }
    public function isFileFormatOk () {
        $imageFileType = $this->imageFileType;
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 5,
                        'sMessage' => "file format is not supported"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return false;
        } else {
            return true;
        }
    }
    public function uploadPhoto () {
        $settings = $this->settings;
        if (isset($settings['needJwtValidation']) && $settings['needJwtValidation']) {
            if(!$this->checkJwt()) return;
        }
        if (!$this->isFileArrayOk()) return;
        if (!$this->isActualImage()) return;
        if (!$this->doesFileAlreadyExists()) return;
        if (!$this->isFileSizeOk()) return;
        if (!$this->isFileFormatOk()) return;
        $target_file = $this->target_file;
        try {
            move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
            $output = [
                'output' => [
                    'success' => true,
                    'status' => [
                        'sCode' => 1,
                        'sMessage' => "the file has been uploaded"
                    ],
                    'output' => []
                ],
                'settings' => $this->settings
            ];
            $this->finalizeOutput($output);
            return;
        } catch (Exception $e) {
            $output = [
            'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 7,
                        'sMessage' => "there is an error uploading file"
//                    'sMessage' => "$e" for debugging
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


