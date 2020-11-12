<?php
namespace PhpAPI;
require_once '/../../autoload.php';
require_once 'userAPI.php';
use PhpAPI\UserAPI as UserAPI;
use PhpAPI\JwtHandler as JwtHandler;
class Router {
    private $methods;
    public function __construct($methods)
    {
        $this->methods = $methods;
    }
    public function stringValidator(&$val) {
        $val = trim(htmlspecialchars(stripslashes($val), ENT_QUOTES, 'utf-8'));
    }
    public function input_validation ($input) {
        if (!is_array($input)):
            return trim(htmlspecialchars(stripslashes($input), ENT_NOQUOTES, 'utf-8'));
        else:
//            return array_walk_recursive($input, [$this, 'stringValidator']);
            array_walk_recursive($input, [$this, "stringValidator"]);
            return $input;
        endif;
    }
    public function __call($method, $arguments)
    {
        extract($this->returnJwtRelatedVars());
        /** @var TYPE_NAME $JWT */
        /** @var TYPE_NAME $isJwtValid */
        if ($JWT && $isJwtValid):
            if (method_exists($this, $method)):
                return call_user_func_array(array($this, $method), $arguments);
            endif;
        elseif ($JWT && !$isJwtValid):
            $settings = ['addJwt' => false, 'echoOrReturn' => 'echo'];
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 1000,
                        'sMessage' => "jwt is not valid, Note: even if you set needJwtValidation=false any provided JWT will investigated!"
                    ],
                    'output' => []
                ],
                'settings' => $settings
            ];
            $this->finalizeOutput($output);
        elseif (!$JWT):
            if (method_exists($this, $method)):
                return call_user_func_array(array($this, $method), $arguments);
            endif;
        endif;
    }
    public function returnJwtIfProvided () {
        $reContentTypeHeader = $this->input_validation($_SERVER["CONTENT_TYPE"]);
        if (substr($reContentTypeHeader, 0, strlen('multipart/form-data')) === 'multipart/form-data'):
            if (isset($_POST['JWT']) && !empty($_POST['JWT'])) return $this->input_validation($_POST['JWT']);
            else return false;
        else:
            $receivedBody = $this->getRequestDataBody();
            if (isset($receivedBody['JWT']) && !empty($receivedBody['JWT'])) return $this->input_validation($receivedBody['JWT']);
            else return false;
        endif;
    }
    public function returnJwtDataIfJwtIsValid ($JWT) {
        try {
            // verify JWT if ok do what you is wanted
            $jwtHandler = new JwtHandler();
            return $jwtHandler->_jwt_decode_data($JWT);
        } catch (\Exception $e) {
            return false;
//            echo $e->getMessage();
        }
    }
    public function returnJwtRelatedVars () {
        $JWT = $this->returnJwtIfProvided();
        if ($JWT) $isJwtValid = $this->returnJwtDataIfJwtIsValid($JWT);
        else $isJwtValid = false;
        return [
            'JWT' => $JWT,
            'isJwtValid' => $isJwtValid
        ];
    }
    public function checkJwt () {
        extract($this->returnJwtRelatedVars());
        /** @var TYPE_NAME $JWT */
        /** @var TYPE_NAME $isJwtValid */
        if ($JWT && $isJwtValid) return true;
        else {
            $settings = ['echoOrReturn' => 'echo'];
            $output = [
                'output' => [
                    'success' => false,
                    'status' => [
                        'sCode' => 1000,
                        'sMessage' => "jwt is not valid, the function or class you want to use need a valid JWT"
                    ],
                    'output' => []
                ],
                'settings' => $settings
            ];
            $this->finalizeOutput($output);
            return false;
        }
    }
    private function lunchRequestedBodyTask()
    {
        $reContentTypeHeader = $this->input_validation($_SERVER["CONTENT_TYPE"]);
        if (substr($reContentTypeHeader, 0, strlen('multipart/form-data')) === 'multipart/form-data') {
            $_POST['extra'] = $this->input_validation($_POST['extra']);
            $_POST['task'] = $this->input_validation($_POST['task']);
            $extra = isset($_POST['extra']) ? $_POST['extra'] : [];
            $userAPI = new UserAPI($_POST['task'], $_POST['extra']);
        } else {
            $receivedBody = $this->getRequestDataBody();
            $task = $this->input_validation($receivedBody['task']);
            $extra = isset($receivedBody['extra']) ? $this->input_validation($receivedBody['extra']) : [];
            $methods = $this->methods;
            if (in_array($task, $methods)) {
                $userAPI = new UserAPI($task, $extra);
            } else {
                $output = [
                    'output' => [
                        'success' => false,
                        'status' => [
                            'sCode' => 0,
                            'sMessage' => "no such a method '$task' in serverSide API"
                        ],
                        'output' => []
                    ],
                    'settings' => ['needJwtValidation' => false, 'addJwt' => false, 'echoOrReturn' => 'echo']
                ];
                $this->finalizeOutput($output);
            }
        }
    }
    private function getRequestDataBody()
    {
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
    public function finalizeOutput($output) {
        $this->decideAboutAddingJwtInOutput($output);
        $output = $this->decideAboutAddingJwtInOutput($output);
        $realOutput = $output['output'];
        $settings = $output['settings'];
        if ($settings['echoOrReturn'] == 'echo'):
            $realOutput = json_encode($realOutput, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            echo $realOutput;
        elseif ($settings['echoOrReturn'] == 'return'):
            return $realOutput;
        endif;
    }
    private function decideAboutAddingJwtInOutput ($output) {
        if (isset($output['settings']['addJwt']) && $output['settings']['addJwt']):
            $jwtHandler = new JwtHandler();
            $nJWT = $jwtHandler->_jwt_encode_data($output['settings']['addJwt']);
            $output['output']['JWT'] = $nJWT;
            return $output;
        else:
            return $output;
        endif;
    }
}

$methods = ['addRow', 'readRows', 'updateRows', 'deleteRows', 'checkColumnHashVerified', 'searchRows'];
$API = new Router($methods);
$API->lunchRequestedBodyTask();