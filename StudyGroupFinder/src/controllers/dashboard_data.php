<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'], $_GET['type'])) {
    echo "Invalid request";
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'];

// Determine SQL query based on request type
if ($type === 'joined') {
    // Include groups user has created or joined
    $sql = "SELECT g.*, gm.user_id AS member_id, g.created_by = '$user_id' AS is_creator 
            FROM groups g 
            LEFT JOIN group_members gm ON g.id = gm.group_id 
            WHERE g.created_by = '$user_id' OR gm.user_id = '$user_id'";
} else {
    // Show only groups user hasn't joined or created
    $sql = "SELECT g.* FROM groups g 
            WHERE g.id NOT IN (
                SELECT group_id FROM group_members WHERE user_id = '$user_id'
            ) AND g.created_by != '$user_id'";
}

$result = $conn->query($sql);

// Generate HTML list
if ($result->num_rows > 0) {
    echo "<ul class='groups-list'>";
    while ($group = $result->fetch_assoc()) {
        echo "<li class='group-item'>";
        
        // Show link for joined groups, otherwise just group name
        if ($type === 'joined') {
            echo "<a href='group.php?id={$group['id']}'>" . 
                 htmlspecialchars($group['name']) . "</a>";
            
            // Determine if user is creator and show appropriate buttons
            if ($group['is_creator']) {
                // Creator sees Delete button
                echo "<button onclick='deleteGroup({$group['id']})'>Delete</button>";
            } else {
                // Non-creator sees Leave button
                echo "<button onclick='leaveGroup({$group['id']})'>Leave</button>";
            }
        } else {
            // Show Join button for available groups
            echo htmlspecialchars($group['name']);
            echo "<button onclick='joinGroup({$group['id']})'>Join</button>";
        }
        
        echo "</li>";
    }
    echo "</ul>";
} else {
    // Show appropriate message based on list type
    if ($type === 'joined') {
        echo "<p>You haven't joined any groups yet.</p>";
    } else {
        echo "<p>No available groups to join.</p>";
    }
}
?>
