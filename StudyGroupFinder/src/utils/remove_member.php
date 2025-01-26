<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $current_user_id = $_SESSION['user_id'];
    
    // Check if current user is the group creator
    $check_leader_sql = "SELECT created_by FROM groups WHERE id = '$group_id'";
    $check_leader_result = $conn->query($check_leader_sql);
    $group = $check_leader_result->fetch_assoc();

    if ($group && $group['created_by'] == $current_user_id) {
        // Record the removal in removed_members table
        $record_removal = "INSERT INTO removed_members (group_id, user_id, removal_time) 
                          VALUES ('$group_id', '$user_id', NOW())";
        $conn->query($record_removal);

        // Delete the member from the group
        $delete_sql = "DELETE FROM group_members WHERE group_id = '$group_id' AND user_id = '$user_id'";
        if ($conn->query($delete_sql) === TRUE) {
            echo json_encode([
                "status" => "success", 
                "message" => "Member removed successfully!",
                "removed_user_id" => $user_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Unauthorized request."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
