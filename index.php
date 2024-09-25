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
    $errId = "X" . uniqid();
    $log->error($e->getMessage(), ['exception' => $e, 'errId' => $errId]);
    if (str_contains($e->getMessage(), "3024 Query execution was interrupted")) {
        http_response_code(408);
        echo json_encode(['error' => 'Query execution timeout', 'errId' => $errId]);
        die();
    }
    http_response_code(500);
    echo json_encode(['error' => 'An exception occurred', 'errId' => $errId]);
    die();
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log) {
    $errId = "E" . uniqid();
    $log->error($errstr, ['errId' => $errId, 'errno' => $errno, 'errfile' => $errfile, 'errline' => $errline]);
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred', 'errId' => $errId]);
    die();
});

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

$pdo = new PDO(
    'mysql:host=db;dbname=' . getenv('DATABASE_DATABASE'),
    getenv('DATABASE_USERNAME'),
    getenv('DATABASE_PASSWORD'),
    $options
);

// Optionally set the max_execution_time for the session (in milliseconds)
$pdo->exec("SET max_execution_time = 1000");

// Use a heavy query that can be interrupted by max_execution_time
$sql = "
        SELECT COUNT(*)
        FROM information_schema.COLUMNS a
        JOIN information_schema.COLUMNS b ON 1
        JOIN information_schema.COLUMNS c ON 1;
    ";

$stmt = $pdo->query($sql);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
