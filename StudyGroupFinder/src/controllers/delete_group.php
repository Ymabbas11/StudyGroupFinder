<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'], $_POST['group_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);

    // Check if user is group leader
    $check_sql = "SELECT created_by FROM groups WHERE id = '$group_id'";
    $check_result = $conn->query($check_sql);
    $group = $check_result->fetch_assoc();

    if ($group && $group['created_by'] == $user_id) {
        // Delete related image files
        $images_sql = "SELECT content FROM messages WHERE group_id = '$group_id' AND type = 'image'";
        $images_result = $conn->query($images_sql);

        // Delete image files
        while ($image = $images_result->fetch_assoc()) {
            $file_path = '../' . $image['content']; // Path relative to web root
            if (file_exists($file_path)) {
                unlink($file_path); // Delete image file
            }
        }

        // Delete group messages
        $messages_sql = "DELETE FROM messages WHERE group_id = '$group_id'";
        $conn->query($messages_sql);

        // Delete group and its members
        $group_sql = "DELETE FROM groups WHERE id = '$group_id'";
        $members_sql = "DELETE FROM group_members WHERE group_id = '$group_id'";
        $sessions_sql = "DELETE FROM sessions WHERE group_id = '$group_id'";

        $conn->query($members_sql);
        $conn->query($sessions_sql);

        if ($conn->query($group_sql)) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to delete group: " . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "You are not authorized to delete this group"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters"
    ]);
}
?>
