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
        } elseif ($action === 'report' && isset($_POST['friend_id'])) { // Report action handling
            $reported_user_id = $_POST['friend_id'];
            // Redirect to the report page
            header("Location: report_user.php?friend_id=" . $reported_user_id);
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
        /* Prevent horizontal scrolling */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

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

        /* Chatbox styles */
        .chat-box {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .chat-message {
            padding: 5px;
        }

        .chat-message.sender {
            background-color: #e0e0e0;
        }

        .chat-message.receiver {
            background-color: #f1f1f1;
        }

        /* Centered Heading */
        .centered-heading {
            text-align: center;
            margin: 30px 0;
        }

        /* List styling */
        .request-list, .friends-list {
            list-style-type: none;
            padding-left: 0;
        }

        .request-list li, .friends-list li {
            background-color: rgba(249, 234, 240, 0.9);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .request-list a, .friends-list a {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .request-list button, .friends-list button {
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
            font-weight: bold;
        }

        .request-list button:hover, .friends-list button:hover {
            background-color: rgb(156, 167, 177);
        }

        .friends-list li a {
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
        }

        .friends-list li a:hover {
            background-color: rgb(156, 167, 177);
        }
    </style>
</head>
<body>

<div class="navbar">
    <span class="exchidea">EXCHIDEA</span>
    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="profile.php">Profile</a>
    </div>
</div>

<h1 class="centered-heading">Friend Requests</h1>
<?php if (mysqli_num_rows($pending_requests) > 0): ?>
    <ul class="request-list">
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

<h1 class="centered-heading">Your Friends</h1>
<?php if (mysqli_num_rows($friends) > 0): ?>
    <ul class="friends-list">
        <?php while ($friend = mysqli_fetch_assoc($friends)): ?>
            <li>
                <?php echo htmlspecialchars($friend['fname']); ?> (<?php echo htmlspecialchars($friend['email']); ?>)
                <a href="view_user.php?user_id=<?php echo $friend['user_id']; ?>">
                    View Profile
                </a>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                    <button type="submit" name="action" value="unfriend">Unfriend</button>
                    <button type="submit" name="action" value="rate">Rate</button>
                    <button type="submit" name="action" value="report">Report</button>
                    <button type="button" onclick="openChatModal(<?php echo $friend['user_id']; ?>)">Chat</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>You have no friends yet.</p>
<?php endif; ?>

<!-- Chat Modal -->
<div id="chatModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Chat</h2>
        <div id="chatBox" class="chat-box"></div>
        <textarea id="chatMessage" rows="4" cols="50" placeholder="Type a message..."></textarea><br><br>
        <button type="button" onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
    var chatModal = document.getElementById("chatModal");
    var chatBox = document.getElementById("chatBox");
    var chatMessageInput = document.getElementById("chatMessage");
    var chatReceiverId = null;

    function openChatModal(friend_id) {
        chatReceiverId = friend_id;
        fetchChatMessages(friend_id);
        chatModal.style.display = "block";
    }

    function fetchChatMessages(friend_id) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_chat.php?friend_id=" + friend_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var messages = JSON.parse(xhr.responseText);
                chatBox.innerHTML = '';
                messages.forEach(function(msg) {
                    var messageDiv = document.createElement('div');
                    messageDiv.classList.add('chat-message');
                    if (msg.sender_id == <?php echo $current_user_id; ?>) {
                        messageDiv.classList.add('sender');
                    } else {
                        messageDiv.classList.add('receiver');
                    }
                    messageDiv.innerHTML = "<strong>" + msg.sender_name + ":</strong> " + msg.message;
                    chatBox.appendChild(messageDiv);
                });
            }
        };
        xhr.send();
    }

    function sendMessage() {
        var message = chatMessageInput.value;
        if (message.trim() === "") return;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "send_chat.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (xhr.status === 200) {
                chatMessageInput.value = '';
                fetchChatMessages(chatReceiverId);
            }
        };
        xhr.send("receiver_id=" + chatReceiverId + "&message=" + encodeURIComponent(message));
    }

    var span = document.getElementsByClassName("close")[0];
    span.onclick = function() {
        chatModal.style.display = "none";
    }
</script>

</body>
</html>
