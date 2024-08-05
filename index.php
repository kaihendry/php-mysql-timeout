<?php
$dbUser = getenv('DATABASE_USERNAME');
$dbPassword = getenv('DATABASE_PASSWORD');
$database = getenv('DATABASE_DATABASE');

$mysqli = new mysqli('db', $dbUser, $dbPassword, $database);

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

echo 'Connection successful!';
?>