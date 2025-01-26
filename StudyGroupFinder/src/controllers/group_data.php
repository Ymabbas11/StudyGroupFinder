<?php
session_start();
include 'db.php'; // Include database connection file

// Check if user is logged in and group ID and request type are provided
if (isset($_SESSION['user_id'], $_GET['group_id'], $_GET['type'])) {
    $user_id = $_SESSION['user_id'];
    $group_id = $conn->real_escape_string($_GET['group_id']);
    $type = $_GET['type'];

    // Get group information
    $group_sql = "SELECT created_by FROM groups WHERE id = '$group_id'";
    $group_result = $conn->query($group_sql);
    $group = $group_result->fetch_assoc();

    // Verify if current user is the group leader
    $is_leader = ($group && $group['created_by'] == $user_id);

    if ($type === 'members') {
        // Return member list
        $members_sql = "SELECT users.id, users.username 
                       FROM users 
                       JOIN group_members ON users.id = group_members.user_id 
                       WHERE group_members.group_id = '$group_id'";
        $members_result = $conn->query($members_sql);

        if ($members_result->num_rows > 0) {
            echo "<ul class='members-list'>";
            while ($member = $members_result->fetch_assoc()) {
                echo "<li class='member-item'>";
                echo htmlspecialchars($member['username']);
                if ($is_leader && $member['id'] != $user_id) {
                    echo "<button onclick='removeMember({$group_id}, {$member['id']})'>Remove</button>";
                }
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No members found.</p>";
        }
    } elseif ($type === 'sessions') {
        // Return session schedule list
        $sessions_sql = "SELECT * FROM sessions WHERE group_id = '$group_id' ORDER BY scheduled_at ASC";
        $sessions_result = $conn->query($sessions_sql);

        if ($sessions_result->num_rows > 0) {
            echo "<ul class='sessions-list'>";
            while ($session = $sessions_result->fetch_assoc()) {
                echo "<li class='session-item'>";
                echo htmlspecialchars($session['title']) . " - " . 
                     date('Y-m-d H:i', strtotime($session['scheduled_at']));
                if ($is_leader) {
                    echo "<button onclick='deleteSession({$session['id']})'>Delete</button>";
                }
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No sessions scheduled.</p>";
        }
    }
} else {
    echo "Invalid request or missing parameters.";
}
?>
