<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_GET['group_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_GET['group_id']);

    // Check if user was recently removed
    $check_removal = "SELECT removal_time FROM removed_members 
                     WHERE group_id = '$group_id' 
                     AND user_id = '$user_id' 
                     AND removal_time > NOW() - INTERVAL 1 MINUTE";
    
    $removal_result = $conn->query($check_removal);
    
    if ($removal_result->num_rows > 0) {
        echo json_encode([
            "status" => "removed",
            "is_member" => false
        ]);
        exit();
    }

    // Check if user is still a member or creator
    $check_sql = "SELECT 1 FROM group_members 
                  WHERE group_id = '$group_id' 
                  AND user_id = '$user_id'
                  UNION
                  SELECT 1 FROM groups
                  WHERE id = '$group_id'
                  AND created_by = '$user_id'";
    
    $result = $conn->query($check_sql);
    
    echo json_encode([
        "status" => "ok",
        "is_member" => $result->num_rows > 0
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "is_member" => false,
        "error" => "Invalid request"
    ]);
}
?>
