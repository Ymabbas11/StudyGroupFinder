<?php
// Start session
session_start();

// Destroy session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: ../index.php");
exit();
?>

