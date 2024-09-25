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

try {
    // Set the connection options
    $options = [
        PDO::ATTR_TIMEOUT => 1, // Timeout in seconds
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];

    // Create a new PDO instance
    $pdo = new PDO('mysql:host=db;dbname=' . getenv('DATABASE_DATABASE'), getenv('DATABASE_USERNAME'), getenv('DATABASE_PASSWORD'), $options);

    $sql = " 
     SELECT /*+ MAX_EXECUTION_TIME(1000) */ BENCHMARK(100000000, MD5('test'));
    ";
    $stmt = $pdo->query($sql);

    // Fetch and print the results
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}