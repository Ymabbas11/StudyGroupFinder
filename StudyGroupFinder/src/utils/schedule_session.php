<?php
// Start session
session_start();
include 'db.php'; // Include database connection file

// Set JSON response header
header('Content-Type: application/json');

// Check if it's a POST request and user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $scheduled_at = $conn->real_escape_string($_POST['scheduled_at']);

    // Verify current user is the group creator
    $check_sql = "SELECT created_by FROM groups WHERE id = '$group_id'";
    $check_result = $conn->query($check_sql);
    $group = $check_result->fetch_assoc();

    if ($group && $group['created_by'] == $user_id) {
        // Check for duplicate session schedules
        $duplicate_sql = "SELECT id FROM sessions 
                         WHERE group_id = '$group_id' 
                         AND scheduled_at = '$scheduled_at'";
        $duplicate_result = $conn->query($duplicate_sql);

        if ($duplicate_result->num_rows > 0) {
            echo json_encode([
                "status" => "error",
                "message" => "A session is already scheduled for this time"
            ]);
            exit();
        }

        // User is group leader, proceed with session scheduling
        $sql = "INSERT INTO sessions (group_id, title, scheduled_at) 
                VALUES ('$group_id', '$title', '$scheduled_at')";

        if ($conn->query($sql)) {
            // Scheduling successful, return JSON success message
            echo json_encode(["status" => "success"]);
        } else {
            // If insertion fails, return JSON error message
            echo json_encode([
                "status" => "error",
                "message" => "Failed to schedule session: " . $conn->error
            ]);
        }
    } else {
        // User not authorized to schedule sessions for this group
        echo json_encode([
            "status" => "error",
            "message" => "You are not authorized to schedule sessions for this group"
        ]);
    }
} else {
    // If not a POST request or not logged in, return unauthorized error
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized request"
    ]);
}
?>
