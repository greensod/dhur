<?php
session_start();

// Check if the admin is logged in and destroy the session for admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {
    // Destroy admin session
    session_unset();
    session_destroy();
    header("Location: index.php");  // Redirect to admin login page
    exit();
}

// Check if a regular user is logged in and destroy their session
if (isset($_SESSION['user_id'])) {
    // Destroy user session
    session_unset();
    session_destroy();
    header("Location: index.php");  // Redirect to the home page or login page
    exit();
} else {
    // In case the session is not set or the user is not logged in
    header("Location: index.php");  // Redirect to home or login page
    exit();
}
?>
