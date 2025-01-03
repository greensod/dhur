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

    // Check if a friend request already exists
    $check_query = "SELECT * FROM friend_requests WHERE sender_id = $current_user_id AND receiver_id = $receiver_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        // Insert friend request
        $insert_query = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES ($current_user_id, $receiver_id, 'pending')";
        mysqli_query($conn, $insert_query);
    } elseif (mysqli_num_rows($check_result) > 0) {
        // Update friend request status to pending if it was previously rejected
        $update_query = "UPDATE friend_requests SET status = 'pending' WHERE sender_id = $current_user_id AND receiver_id = $receiver_id";
        mysqli_query($conn, $update_query);
    }
}

header("Location: view_user.php?user_id=$receiver_id");
exit;
?>