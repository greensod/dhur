<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$current_user_email = $_SESSION['user_email'];
$query = "SELECT user_id FROM user WHERE email = '$current_user_email'";
$result = mysqli_query($conn, $query);
$current_user = mysqli_fetch_assoc($result);
$current_user_id = $current_user['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sender_id'])) {
    $sender_id = $_POST['sender_id'];

    // Update the friend request status to rejected
    $update_query = "UPDATE friend_requests SET status = 'rejected' WHERE sender_id = $sender_id AND receiver_id = $current_user_id";
    mysqli_query($conn, $update_query);
}

header("Location: view_user.php?user_id=$sender_id");
exit;
?>