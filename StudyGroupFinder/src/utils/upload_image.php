<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'], $_POST['group_id'], $_FILES['image'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);

    // Image file information
    $file = $_FILES['image'];
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = '../uploads/' . $fileName;

    // Try to move uploaded file and log errors
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $fileUrl = 'uploads/' . $fileName;

        // Insert image message into database
        $sql = "INSERT INTO messages (group_id, user_id, content, type) VALUES (?, ?, ?, 'image')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $group_id, $user_id, $fileUrl);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "file_url" => $fileUrl]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Failed to upload file: " . error_get_last()['message']
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Missing required parameters"
    ]);
}
?>
