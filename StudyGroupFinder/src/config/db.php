<?php
// Load configuration from environment variables or a separate config file
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: '';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: '';
$port = getenv('DB_PORT') ?: '3306';

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
