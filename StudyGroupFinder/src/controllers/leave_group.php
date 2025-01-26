<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'], $_POST['group_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);

    // Delete member from group_members table
    $sql = "DELETE FROM group_members WHERE user_id = '$user_id' AND group_id = '$group_id'";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to leave group: " . $conn->error
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters"
    ]);
}
?>
