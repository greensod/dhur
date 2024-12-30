<?php
session_start();
include 'connection.php';
include 'friend_functions.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Fetch the logged-in user details
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$current_user_id = $user['user_id'];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $request_id = $_POST['request_id'] ?? null;

        if ($action === 'accept' && $request_id) {
            acceptFriendRequest($request_id);
        } elseif ($action === 'reject' && $request_id) {
            rejectFriendRequest($request_id);
        }
    }
}

// Fetch pending friend requests
$pending_requests = getPendingFriendRequests($current_user_id);

// Fetch friends list
$friends = getFriendsList($current_user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
</head>
<body>
    <h1>Friend Requests</h1>
    <?php if (mysqli_num_rows($pending_requests) > 0): ?>
        <ul>
            <?php while ($request = mysqli_fetch_assoc($pending_requests)): ?>
                <li>
                Friend Request from: 
    <a href="view_user.php?user_id=<?php echo $request['sender_id']; ?>">
        <?php echo htmlspecialchars($request['fname']); ?>
    </a>
    <form method="POST" style="display: inline;">
        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
        <button type="submit" name="action" value="accept">Accept</button>
        <button type="submit" name="action" value="reject">Reject</button>
    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No pending friend requests.</p>
    <?php endif; ?>

    <h1>Your Friends</h1>
    <?php if (mysqli_num_rows($friends) > 0): ?>
        <ul>
            <?php while ($friend = mysqli_fetch_assoc($friends)): ?>
                <li>
                    <?php echo htmlspecialchars($friend['fname']); ?> (<?php echo htmlspecialchars($friend['email']); ?>)
                    <a href="view_user.php?user_id=<?php echo $friend['user_id']; ?>">
                        <button type="button">View Profile</button>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>You have no friends yet.</p>
    <?php endif; ?>

</body>
</html>