<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'], $_POST['group_id'], $_POST['type'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_POST['group_id']);
    $type = $_POST['type'];
    
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle different message types
    if ($type === 'text' && isset($_POST['message']) && trim($_POST['message']) !== "") {
        // Handle text message
        $message = $conn->real_escape_string($_POST['message']);
        $sql = "INSERT INTO messages (group_id, user_id, content, type) VALUES (?, ?, ?, 'text')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $group_id, $user_id, $message);
        
    } elseif ($type === 'file' || $type === 'image') {
        // Handle file/image upload
        $uploadedFile = isset($_FILES['file']) ? $_FILES['file'] : $_FILES['image'];
        $fileName = time() . '_' . basename($uploadedFile['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
            $fileUrl = 'uploads/' . $fileName;
            
            // For files, store the original filename
            if ($type === 'file') {
                $originalName = $conn->real_escape_string($uploadedFile['name']);
                $sql = "INSERT INTO messages (group_id, user_id, content, type, file_name) VALUES (?, ?, ?, 'image', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiss", $group_id, $user_id, $fileUrl, $originalName);
            } else {
                $sql = "INSERT INTO messages (group_id, user_id, content, type) VALUES (?, ?, ?, 'image')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $group_id, $user_id, $fileUrl);
            }
        } else {
            echo json_encode([
                "status" => "error", 
                "message" => "Failed to upload file: " . error_get_last()['message']
            ]);
            exit();
        }
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Invalid message type or empty content"
        ]);
        exit();
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Database error: " . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Missing required parameters"
    ]);
}
?>
