<?php
include 'connection.php';
session_start();

// If the user is already logged in, redirect to the admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get email and password from the form
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Consider using bcrypt or a more secure hashing method in production

    // Prepare the query to check the admin credentials
    $admin_query = "SELECT id FROM admin WHERE email = ? AND password = ?";
    $admin_stmt = $conn->prepare($admin_query);
    $admin_stmt->bind_param("ss", $email, $password);
    $admin_stmt->execute();
    $admin_stmt->store_result();

    // Check if any record matched the admin credentials
    if ($admin_stmt->num_rows > 0) {
        // Admin authenticated
        $admin_stmt->bind_result($admin_id);
        $admin_stmt->fetch();
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin_id;
        header("Location: admin.php");
        exit();
    } else {
        // Invalid credentials
        header("Location: admin_login.php?error=Invalid%20credentials");
        exit();
    }
}
?>