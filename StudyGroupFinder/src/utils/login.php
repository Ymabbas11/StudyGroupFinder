<?php
// Start session
session_start();
include 'db.php'; // Include database connection file

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize user input
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Query user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Username exists, check password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password matches, set session variables and redirect to dashboard
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            // Password doesn't match, return error message
            header("Location: ../index.php?error=Invalid password");
            exit();
        }
    } else {
        // Username doesn't exist, return error message
        header("Location: ../index.php?error=Username not found");
        exit();
    }
} else {
    // If not a POST request, redirect to homepage
    header("Location: ../index.php");
    exit();
}
?>
