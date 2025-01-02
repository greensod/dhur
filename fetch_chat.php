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

if (isset($_GET['friend_id'])) {
    $friend_id = $_GET['friend_id'];

    // Fetch messages between the current user and the friend
    $query = "
        SELECT m.*, u.fname AS sender_name
        FROM messages m
        JOIN user u ON m.sender_id = u.user_id
        WHERE (m.sender_id = $current_user_id AND m.receiver_id = $friend_id)
           OR (m.sender_id = $friend_id AND m.receiver_id = $current_user_id)
        ORDER BY m.created_at ASC
    ";
    $result = mysqli_query($conn, $query);
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($messages);
}
?>