<?php
// Start session
session_start();

// Include database connection file
include 'db.php';

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query all groups that user hasn't joined
    $sql = "SELECT g.* FROM groups g 
            WHERE g.id NOT IN (
                SELECT group_id FROM group_members WHERE user_id = '$user_id'
            ) AND g.created_by != '$user_id'";
    $result = $conn->query($sql);

    $groups = [];
    // Add each group to results array
    while ($group = $result->fetch_assoc()) {
        $groups[] = $group;
    }

    // Return JSON response with group list
    header('Content-Type: application/json');
    echo json_encode($groups);
} else {
    // If user is not logged in, return unauthorized error message
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access"]);
}
?>
