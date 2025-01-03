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

// Check for unread messages
$query = "
    SELECT sender_id, COUNT(*) as unread_count
    FROM messages
    WHERE receiver_id = $current_user_id AND is_read = 0
    GROUP BY sender_id
";
$result = mysqli_query($conn, $query);
$unread_messages = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($unread_messages);
?>
