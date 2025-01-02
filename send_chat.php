<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Fetch current user ID
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$current_user_id = $user['user_id'];

if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Insert the message into the database
    $query = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($current_user_id, $receiver_id, '$message')";
    mysqli_query($conn, $query);
}
?>