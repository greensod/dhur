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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_id'])) {
    $receiver_id = $_POST['receiver_id'];

    // Delete the friend request
    $delete_query = "DELETE FROM friend_requests WHERE sender_id = $current_user_id AND receiver_id = $receiver_id";
    mysqli_query($conn, $delete_query);
}

header("Location: view_user.php?user_id=$receiver_id");
exit;
?>