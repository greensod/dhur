<?php
include 'connection.php';

// Send a friend request
function sendFriendRequest($sender_id, $receiver_id) {
    global $conn;
    $query = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES ('$sender_id', '$receiver_id', 'pending')";
    return mysqli_query($conn, $query);
}

// Accept a friend request
function acceptFriendRequest($request_id) {
    global $conn;
    $query = "UPDATE friend_requests SET status = 'accepted' WHERE request_id = '$request_id'";
    if (mysqli_query($conn, $query)) {
        $request_query = "SELECT sender_id, receiver_id FROM friend_requests WHERE request_id = '$request_id'";
        $result = mysqli_query($conn, $request_query);
        if ($row = mysqli_fetch_assoc($result)) {
            $query2 = "INSERT INTO friends (user1_id, user2_id) VALUES ('{$row['sender_id']}', '{$row['receiver_id']}')";
            return mysqli_query($conn, $query2);
        }
    }
    return false;
}

// Reject or delete a friend request
function rejectFriendRequest($request_id) {
    global $conn;
    $query = "UPDATE friend_requests SET status = 'rejected' WHERE request_id = '$request_id'";
    return mysqli_query($conn, $query);
}

// Get pending friend requests
function getPendingFriendRequests($user_id) {
    global $conn;
    $query = "SELECT * FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending'";
    return mysqli_query($conn, $query);
}

// Unfriend a user
function unfriendUser($user1_id, $user2_id) {
    global $conn;
    $query = "DELETE FROM friends WHERE (user1_id = '$user1_id' AND user2_id = '$user2_id') OR (user1_id = '$user2_id' AND user2_id = '$user1_id')";
    mysqli_query($conn, $query);
    return mysqli_query($conn, $query);
}


// Get the friend list
function getFriendsList($user_id) {
    global $conn;
    $query = "
        SELECT u.user_id, u.fname, u.email
        FROM friends f
        JOIN user u ON (u.user_id = f.user1_id OR u.user_id = f.user2_id)
        WHERE (f.user1_id = '$user_id' OR f.user2_id = '$user_id') AND u.user_id != '$user_id'
    ";
    return mysqli_query($conn, $query);
}
?>