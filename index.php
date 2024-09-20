<?php

set_exception_handler(function ($exception) {
    http_response_code(500);
});

// Set a custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
});

require 'vendor/autoload.php'; // This autoloads Monolog

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\ErrorHandler;

$log = new Logger('name');
$stderrHandler = new StreamHandler('php://stdout', Logger::DEBUG);
$stderrHandler->setFormatter(new JsonFormatter());
$log->pushHandler($stderrHandler);

ErrorHandler::register($log);

$mysqli = new mysqli('db', getenv('DATABASE_USERNAME'), getenv('DATABASE_PASSWORD'), getenv('DATABASE_DATABASE'));

if ($mysqli->connect_error) {
    throw new Exception('Database Connection Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// check performace_schema is enabled
$performanceSchemaQuery = "SHOW VARIABLES LIKE 'performance_schema';";
$result = $mysqli->query($performanceSchemaQuery);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Variable: " . $row['Variable_name'] . "<br>";
    echo "Value: " . $row['Value'] . "<br>";
} else {
    throw new Exception("Unable to fetch performance schema");
}

$mysqli->close();

$log->info('database connection successful');


