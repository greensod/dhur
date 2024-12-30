<?php
session_start();
include 'connection.php';
include 'friend_functions.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Fetch logged-in user details
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$current_user = mysqli_fetch_assoc($result);
$current_user_id = $current_user['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['receiver_id'];

    // Check if the request is valid
    if (!is_numeric($receiver_id) || $receiver_id == $current_user_id) {
        die("Invalid receiver.");
    }

    // Send the friend request
    if (sendFriendRequest($current_user_id, $receiver_id)) {
        echo "Friend request sent successfully.";
    } else {
        echo "Failed to send friend request.";
    }
}
?>
