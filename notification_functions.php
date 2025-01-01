<?php
include 'connection.php';

function getUnreadNotificationCount($user_id) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

function getUnreadNotifications($user_id) {
    global $conn;
    $query = "SELECT sender_id, request_id FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending'";
    return mysqli_query($conn, $query);
}

function markNotificationsAsRead($user_id) {
    global $conn;
    $query = "UPDATE friend_requests SET status = 'viewed' WHERE receiver_id = '$user_id' AND status = 'pending'";
    return mysqli_query($conn, $query);
}
?>
