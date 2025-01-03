<?php
include 'connection.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Update the user's is_banned field to 1 (ban the user)
    $query = "UPDATE user SET is_banned = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Redirect back to the manage users page with a success message
        $_SESSION['message'] = "User has been banned successfully.";
        header("Location: manage_users.php");
        exit();
    } else {
        // In case of an error
        $_SESSION['error'] = "Failed to ban the user. Please try again.";
        header("Location: manage_users.php");
        exit();
    }

    $stmt->close();
} else {
    // In case no user_id is provided
    $_SESSION['error'] = "No user selected for banning.";
    header("Location: manage_users.php");
    exit();
}

$conn->close();
?>
