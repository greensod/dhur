<?php
// Database Connection (Assume connection details are already defined in db.php)
require_once 'connection.php';

// Fetch Unread Notifications Count for Navbar
function fetchNotificationCount($userId, $conn) {
    $sql = "SELECT COUNT(*) AS unread_count FROM friend_requests WHERE recipient_id = ? AND checked_status = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['unread_count'] ?? 0;
}

// Fetch Friend Requests
function fetchFriendRequests($userId, $conn) {
    $sql = "SELECT sender_id, sent_at FROM friend_requests WHERE recipient_id = ? ORDER BY sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    return $stmt->get_result();
}

// Mark Notifications as Read
function markNotificationsRead($userId, $conn) {
    $sql = "UPDATE friend_requests SET checked_status = 1 WHERE recipient_id = ? AND checked_status = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    return $stmt->execute();
}

// Handle Navbar Display
session_start();
$userId = $_SESSION['user_id']; // Assuming the user is logged in
$unreadCount = fetchNotificationCount($userId, $conn);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <style>
        .notification-icon {
            position: relative;
            cursor: pointer;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 8px;
            font-size: 12px;
        }
        .dropdown {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .dropdown.show {
            display: block;
        }
        .dropdown-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
        }
    </script>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <!-- Other menu items -->
    <div class="notification-icon" onclick="toggleDropdown()">
        <img src="notification_icon.png" alt="Notifications" width="30">
        <?php if ($unreadCount > 0): ?>
            <span class="notification-badge"><?php echo $unreadCount; ?></span>
        <?php endif; ?>
    </div>
    <div id="notificationDropdown" class="dropdown">
        <?php
        $requests = fetchFriendRequests($userId, $conn);
        if ($requests->num_rows > 0):
            while ($row = $requests->fetch_assoc()):
                echo '<div class="dropdown-item">';
                echo 'Friend Request from User ID: ' . htmlspecialchars($row['sender_id']);
                echo ' at ' . htmlspecialchars($row['sent_at']);
                echo '</div>';
            endwhile;
        else:
            echo '<div class="dropdown-item">No new friend requests.</div>';
        endif;
        ?>
    </div>
</div>

<!-- PHP Logic to Mark Notifications as Read on Dropdown Open -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['markRead'])) {
    markNotificationsRead($userId, $conn);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>

</body>
</html>
