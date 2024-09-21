<?php

require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\ErrorHandler;

$log = new Logger('name');
$stderrHandler = new StreamHandler('php://stderr', Logger::DEBUG);
$stderrHandler->setFormatter(new JsonFormatter());
$log->pushHandler($stderrHandler);

ErrorHandler::register($log);

set_exception_handler(function ($e) use ($log) {
    $errId = uniqid();
    $log->error($e->getMessage(), ['exception' => $e, 'errId' => $errId]);
    http_response_code(500);
    echo json_encode(['error' => 'An exception occurred', 'errId' => $errId]);
    die();
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log) {
    $errId = uniqid();
    $log->error($errstr, ['errId' => $errId, 'errno' => $errno, 'errfile' => $errfile, 'errline' => $errline]);
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred', 'errId' => $errId]);
    die();
});

throw new Exception('hiii');
// trigger_error('Error', E_USER_ERROR);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = mysqli_connect('db', getenv('DATABASE_USERNAME'), getenv('DATABASE_PASSWORD'), getenv('DATABASE_DATABASE'));
