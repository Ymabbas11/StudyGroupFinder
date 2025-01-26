<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $session_id = $conn->real_escape_string($_POST['session_id']);
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $user_id = $_SESSION['user_id'];

    // Check if user is group creator
    $check_creator = "SELECT created_by FROM groups WHERE id = '$group_id'";
    $creator_result = $conn->query($check_creator);
    $group = $creator_result->fetch_assoc();

    if ($group && $group['created_by'] == $user_id) {
        // Delete the session
        $delete_sql = "DELETE FROM sessions WHERE id = '$session_id' AND group_id = '$group_id'";
        if ($conn->query($delete_sql)) {
            echo json_encode(["status" => "success", "message" => "Session deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting session"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Unauthorized action"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
