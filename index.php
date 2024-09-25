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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = mysqli_init();
$mysqli->real_connect('db', getenv('DATABASE_USERNAME'), getenv('DATABASE_PASSWORD'), getenv('DATABASE_DATABASE'));

$sql = "
    SELECT /*+ MAX_EXECUTION_TIME(1000) */ BENCHMARK(100000000, MD5('test'));
";
$result = $mysqli->query($sql);
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
