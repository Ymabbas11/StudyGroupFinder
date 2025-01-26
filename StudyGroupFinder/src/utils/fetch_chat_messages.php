<?php
// Start session
session_start();
include 'db.php'; // Include database connection file

// Set response header to JSON format
header('Content-Type: application/json');

// Check if user is logged in and group ID is provided
if (isset($_SESSION['user_id'], $_GET['group_id'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_GET['group_id']);

    // Get chat messages for this group, ordered by time
    $sql = "SELECT m.*, u.username 
            FROM messages m 
            JOIN users u ON m.user_id = u.id 
            WHERE m.group_id = ? 
            ORDER BY m.sent_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($message = $result->fetch_assoc()) {
        $messages[] = [
            'id' => $message['id'],
            'content' => $message['content'],
            'type' => $message['type'],
            'username' => $message['username'],
            'sent_at' => $message['sent_at']
        ];
    }
    
    echo json_encode($messages);
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized access or missing group ID"
    ]);
}
?>
