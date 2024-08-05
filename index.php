<?php
$dbUser = getenv('DATABASE_USERNAME');
$dbPassword = getenv('DATABASE_PASSWORD');
$database = getenv('DATABASE_DATABASE');

$mysqli = new mysqli('db', $dbUser, $dbPassword, $database);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// check performace_schema is enabled
$performanceSchemaQuery =  "SHOW VARIABLES LIKE 'performance_schema';";
$result = $mysqli->query($performanceSchemaQuery);
if ($result) {
    // Fetch the result
    $row = $result->fetch_assoc();
    echo "Variable: " . $row['Variable_name'] . "<br>";
    echo "Value: " . $row['Value'] . "<br>";
} else {
    echo "Error: " . $conn->error;
}

$mysqli->close();

?>