<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'], $_POST['group_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);

    // Join group
    $sql = "INSERT INTO group_members (group_id, user_id) VALUES ('$group_id', '$user_id')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to join group: " . $conn->error
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters"
    ]);
}
?>
