<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO groups (name, description, created_by) VALUES ('$name', '$description', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Group created successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error creating group."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
