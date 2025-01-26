<?php
// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();
include 'php/db.php'; // Include database connection file

// Check if user is logged in and group ID is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['group_id'])) {
    header("Location: index.php");
    exit();
}

$group_id = $conn->real_escape_string($_GET['group_id']);
$user_id = $_SESSION['user_id'];

// Get group information
$group_sql = "SELECT * FROM groups WHERE id = '$group_id'";
$group_result = $conn->query($group_sql);
$group = $group_result ? $group_result->fetch_assoc() : null;

if (!$group) {
    echo "Group not found.";
    exit();
}

// Check if user is group leader
$is_creator = ($group['created_by'] == $user_id);

// Get group members
$members_sql = "SELECT users.id, users.username FROM users 
                JOIN group_members ON users.id = group_members.user_id 
                WHERE group_members.group_id = '$group_id'";
$members_result = $conn->query($members_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($group['name']); ?> - Study Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Study Group Finder</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <!-- Left Column: Chat and Messages -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo htmlspecialchars($group['name']); ?> - Group Chat</h5>
                    </div>
                    <div class="card-body">
                        <div id="chat-box" class="chat-container mb-3"></div>
                        <form id="chat-form" onsubmit="sendMessage(event)" class="d-flex gap-2">
                            <input type="hidden" id="group_id" value="<?php echo $group_id; ?>">
                            <input type="text" id="message" class="form-control" placeholder="Type a message...">
                            <label class="btn btn-outline-primary mb-0">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="fileUpload" class="d-none">
                            </label>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Group Info and Members -->
            <div class="col-md-4">
                <!-- Group Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Group Information</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?php echo htmlspecialchars($group['description']); ?></p>
                    </div>
                </div>

                <!-- Members List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Members</h5>
                    </div>
                    <div class="card-body">
                        <div id="members-list"></div>
                    </div>
                </div>

                <!-- Sessions List -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Study Sessions</h5>
                        <?php if ($is_creator): ?>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                            <i class="fas fa-plus"></i> Schedule Session
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div id="sessions-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Session Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Schedule Study Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="schedule-form">
                        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                        <div class="mb-3">
                            <label for="sessionTitle" class="form-label">Session Title</label>
                            <input type="text" class="form-control" id="sessionTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="sessionDateTime" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="sessionDateTime" name="scheduled_at" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Schedule Session</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Image preview container -->
                    <div id="downloadImagePreviewContainer" style="display: none;">
                        <img id="modalImage" src="" alt="Preview" style="max-width: 100%;">
                    </div>
                    <!-- File info container -->
                    <div id="downloadFileInfoContainer" style="display: none;">
                        <i id="downloadFileIcon" class="fas fa-file fa-4x mb-3"></i>
                        <h4 id="downloadFileName" class="mb-3"></h4>
                        <p id="downloadFileType" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadButton" href="" download class="btn btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this modal right after your existing image modal -->
    <div class="modal fade" id="sendPreviewModal" tabindex="-1" aria-labelledby="sendPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendPreviewModalLabel">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="imagePreviewContainer" style="display: none;">
                        <img id="sendPreviewImage" src="" alt="Preview" style="max-width: 100%;">
                    </div>
                    <div id="fileInfoContainer" style="display: none;">
                        <i id="fileIcon" class="fas fa-file fa-4x mb-3"></i>
                        <h4 id="fileName" class="mb-3"></h4>
                        <p id="fileSize" class="mb-2"></p>
                        <p id="fileType" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAndSendFile()">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    </script>
    <script src="js/group-chat.js"></script>
</body>
</html>
