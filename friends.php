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
        } elseif ($action === 'unfriend' && isset($_POST['friend_id'])) {
            $friend_id = $_POST['friend_id'];
            unfriendUser($current_user_id, $friend_id);
        } elseif ($action === 'rate' && isset($_POST['friend_id'])) {
            header("Location: rate_user.php?friend_id=" . $_POST['friend_id']);
            exit;
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
    <style>
        .navbar {
            width: 100%;
            background-color: rgba(249, 234, 240, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
        }

        .navbar .nav-links {
            display: flex;
            gap: 15px;
            margin-right: 30px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .navbar .nav-links a:hover {
            background-color: rgb(156, 167, 177);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background-color: rgb(240, 164, 201);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(156, 167, 177);
        }
    </style>
</head>
<body>

<div class="navbar">
    <span class="exchidea">EXCHIDEA</span>
    <div class="nav-links">
        <a href="profile.php">Profile</a>
        <a href="home.php">Home</a>
    </div>
</div>

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
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                    <button type="submit" name="action" value="unfriend">Unfriend</button>
                    <button type="submit" name="action" value="rate">Rate</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>You have no friends yet.</p>
<?php endif; ?>

<!-- Rating Modal -->
<div id="rateModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Rate Friend</h2>
        <form id="rateForm" method="POST" action="rate_user.php">
            <input type="hidden" name="friend_id" id="friend_id">
            <label for="rating">Rating (1-5):</label>
            <select name="rating" id="rating" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select><br><br>

            <label for="review">Review (Optional):</label><br>
            <textarea name="review" id="review" rows="4" cols="50"></textarea><br><br>

            <button type="submit">Submit Rating</button>
        </form>
    </div>
</div>

<script>
    var modal = document.getElementById("rateModal");
    var span = document.getElementsByClassName("close")[0];

    function openRateModal(friend_id) {
        document.getElementById("friend_id").value = friend_id;
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
