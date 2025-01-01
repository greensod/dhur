<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];

// Get the logged-in user's interests
$query = "SELECT interests FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$user_interests = $user ? explode(',', $user['interests']) : [];

// Fetch other users whose skills match the logged-in user's interests
if (!empty($user_interests)) {
    $interests_condition = array_map(function ($interest) use ($conn) {
        return "FIND_IN_SET('" . mysqli_real_escape_string($conn, trim($interest)) . "', us.skill_name)";
    }, $user_interests);
    $interests_condition = implode(' OR ', $interests_condition);

    $query = "
        SELECT u.user_id, u.fname, us.skill_name, us.level, us.duration
        FROM user u
        JOIN user_skills us ON u.user_id = us.user_id
        WHERE u.email != '$user_email' AND ($interests_condition)
    ";
    $result = mysqli_query($conn, $query);
    $matches = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $matches[] = $row;
        }
    } else {
        $message = "No matches found based on your interests.";
    }
} else {
    $message = "You have not set any interests.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/notifications.css">
    <style>
        /* Prevent horizontal scrolling */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        /* Navbar */
        .navbar {
            width: 100%;
            background-color: rgba(249, 234, 240, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Left part of the navbar (logo) */
        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
        }

        /* Right part of the navbar (buttons) */
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

        /* Navbar search bar */
        .navbar .search-bar {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-grow: 1; /* Allow the search bar to grow and center */
            max-width: 400px; /* Max width for search bar */
            margin: 0 auto; /* Center the search bar */
        }

        /* Form elements in the search bar */
        .navbar .search-bar select,
        .navbar .search-bar input,
        .navbar .search-bar button {
            padding: 5px;
            font-size: 14px;
        }

        .navbar .search-bar input {
            width: 200px;
        }

        /* Matches table styling */
        .matches-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            background-color: rgba(249, 234, 240, 0.9);
        }
        .matches-table a {
            text-decoration: none; /* Removes the underline */
            color: black;
        }
  

        .matches-table th, .matches-table td {
            border: 1px solid #ecbfbf;
            padding: 10px;
            text-align: left;
        }

        .matches-table th {
            background-color: #efc9c9;
        }
        
        .search-bar button{
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            border: none;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <span class="exchidea">EXCHIDEA</span>
        <div class="search-bar">
            <form method="POST" action="search_results.php">
                <select name="search_type" required>
                    <option value="">Select</option>
                    <option value="skill">Skill</option>
                    <option value="interest">Interest</option>
                </select>
                <input type="text" name="search_term" placeholder="Search..." required>
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="nav-links">
            <a href="profile.php">Profile</a>
            <a href="add_skills.php">Add Skills</a>
            <a href="friends.php">Friends</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    

    <div class="notification-icon">
        <img src="notification_icon.png" alt="Notifications" id="notificationIcon">
        <span class="badge" id="notificationCount"><?php echo $notification_count; ?></span>
        <div id="notificationDropdown" class="dropdown">
            <?php if (mysqli_num_rows($notifications) > 0): ?>
                <ul>
                    <?php while ($notification = mysqli_fetch_assoc($notifications)): ?>
                        <li>
                            You have received a friend request from User ID: <?php echo htmlspecialchars($notification['sender_id']); ?>
                            <form method="POST" action="friends.php">
                                <input type="hidden" name="request_id" value="<?php echo $notification['request_id']; ?>">
                                <button type="submit" name="action" value="accept">Accept</button>
                                <button type="submit" name="action" value="reject">Reject</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No new notifications.</p>
            <?php endif; ?>
        </div>
    </div>

    <h1>Welcome to Exchidea, <?php echo htmlspecialchars($user_name); ?>!</h1>
    <p>Your interests are: <strong><?php echo htmlspecialchars(implode(', ', $user_interests)); ?></strong></p>

    <h2>Matching Users Based on Skill:</h2>
    <?php if (!empty($matches)): ?>
        <table class="matches-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Skill</th>
                    <th>Level</th>
                    <th>Available Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $match): ?>
                    <tr>
                        <td>
                            <a href="view_user.php?user_id=<?php echo $match['user_id']; ?>">
                                <?php echo htmlspecialchars($match['fname']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($match['skill_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['level']); ?></td>
                        <td><?php echo htmlspecialchars($match['duration']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <script src="js/notifications.js"></script>


</body>
</html>
