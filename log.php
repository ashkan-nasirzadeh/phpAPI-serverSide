<?php
namespace PhpAPI;
include_once __DIR__.'/../../autoload.php';
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter as LineFormatter;
class Log {
    public function warning($msg) {
        $log = new Logger('phpAPILoggerChannel');
        $monolog_streamHandler = new StreamHandler('log.log', Logger::WARNING);
        $monolog_loggerTimeFormat = 'H:i:s';
        $monolog_formatter = new LineFormatter("\n-------------LOG-------------\n[%datetime%] %message%\n", $monolog_loggerTimeFormat);
        $monolog_streamHandler->setFormatter($monolog_formatter);
        $log->pushHandler($monolog_streamHandler);
        $log->warning($msg);
    }
}