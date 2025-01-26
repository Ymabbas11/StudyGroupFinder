<?php
// Database configuration template
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "study_group_finder";
$port = "3306"; // Default MySQL port

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 