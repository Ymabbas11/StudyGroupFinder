<?php
// Start session
session_start();
include 'db.php';

// Check if user is logged in and group ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['group_id'])) {
    header("Location: ../index.php");
    exit();
}

$group_id = $conn->real_escape_string($_GET['group_id']);
$user_id = $_SESSION['user_id'];

// Get group information
$group_sql = "SELECT * FROM groups WHERE id = '$group_id'";
$group_result = $conn->query($group_sql);
$group = $group_result->fetch_assoc();

// Check if current user is the group leader
if ($group['created_by'] != $user_id) {
    header("Location: ../dashboard.php?message=You are not authorized to access this page.");
    exit();
}

// Get group members
$members_sql = "SELECT users.id, users.username FROM users 
                JOIN group_members ON users.id = group_members.user_id 
                WHERE group_members.group_id = '$group_id'";
$members_result = $conn->query($members_sql);

// Get scheduled sessions
$sessions_sql = "SELECT * FROM sessions WHERE group_id = '$group_id' ORDER BY scheduled_at ASC";
$sessions_result = $conn->query($sessions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $group['name']; ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <h1><?php echo $group['name']; ?> - Admin Dashboard</h1>

    <!-- Member List -->
    <h2>Manage Group Members</h2>
    <ul class="group-list">
        <?php while ($member = $members_result->fetch_assoc()): ?>
            <li class="group-item">
                <?php echo $member['username']; ?>
                <?php if ($member['id'] != $user_id): ?>
                    <!-- Remove Member Form -->
                    <form action="remove_member.php" method="POST" class="remove-form">
                        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $member['id']; ?>">
                        <button type="submit" class="remove-button">Remove</button>
                    </form>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Scheduled Sessions -->
    <h2>Scheduled Sessions</h2>
    <?php if ($sessions_result->num_rows > 0): ?>
        <ul class="session-list">
            <?php while ($session = $sessions_result->fetch_assoc()): ?>
                <li>
                    <?php echo $session['title']; ?> - <?php echo $session['scheduled_at']; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No sessions scheduled.</p>
    <?php endif; ?>

    <!-- Schedule New Session -->
    <h2>Schedule a New Session</h2>
    <form action="schedule_session.php" method="POST" class="schedule-form">
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
        <label for="title">Session Title:</label>
        <input type="text" name="title" required>
        
        <label for="scheduled_at">Date & Time:</label>
        <input type="datetime-local" name="scheduled_at" required>
        
        <button type="submit">Schedule Session</button>
    </form>

    <a href="../dashboard.php">Back to Dashboard</a>
</body>
</html> 