<?php
session_start();
include 'db.php';

$group_id = $conn->real_escape_string($_GET['group_id']);
$user_id = $_SESSION['user_id'];

// Check if user is group leader
$check_sql = "SELECT created_by FROM groups WHERE id = '$group_id'";
$check_result = $conn->query($check_sql);
$group = $check_result->fetch_assoc();

// Get session schedules
$sessions_sql = "SELECT * FROM sessions WHERE group_id = '$group_id' ORDER BY scheduled_at ASC";
$sessions_result = $conn->query($sessions_sql);

if ($sessions_result->num_rows > 0) {
    echo "<ul class='sessions-list'>";
    while ($session = $sessions_result->fetch_assoc()) {
        echo "<li class='session-item'>";
        echo htmlspecialchars($session['title']) . " - " . 
             date('Y-m-d H:i', strtotime($session['scheduled_at']));
        if ($group && $group['created_by'] == $user_id) {
            echo "<button onclick='deleteSession({$session['id']})'>Delete</button>";
        }
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No sessions scheduled.</p>";
}
?>
