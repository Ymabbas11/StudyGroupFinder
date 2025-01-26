<?php
// Start session
session_start();
include 'db.php'; // Include database connection file

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize user input
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Username or email already exists, return error message
        header("Location: ../index.php?error=Username or email already exists");
        exit();
    }

    // Hash password and insert new user data
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Using bcrypt for encryption
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql)) {
        // Registration successful, redirect to homepage with success message
        header("Location: ../index.php?message=Registration successful. Please login.");
        exit();
    } else {
        // If insertion fails, show error
        header("Location: ../index.php?error=Registration failed: " . $conn->error);
        exit();
    }
} else {
    // If not a POST request, redirect to homepage
    header("Location: ../index.php");
    exit();
}
?>
