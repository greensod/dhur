<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];

// Function to count pending friend requests
function countPendingFriendRequests($user_id) {
    global $conn;
    $query = "SELECT COUNT(*) AS pending_count FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['pending_count'];
}

// Get the current user's ID
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$current_user_id = $user['user_id'];

// Get the count of pending friend requests
$pending_count = countPendingFriendRequests($current_user_id);

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
    <style>
        /* Prevent horizontal scrolling */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden;
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
            color: rgb(161, 96, 97);
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .navbar .nav-links a:hover {
            background-color: rgb(156, 167, 177);
        }

        .navbar .search-bar {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-grow: 1;
            max-width: 400px;
            margin: 0 auto;
        }

        .navbar .search-bar select,
        .navbar .search-bar input,
        .navbar .search-bar button {
            padding: 5px;
            font-size: 14px;
            color: rgb(161, 96, 97);
            font-weight: bold;

        }
        
        .navbar .search-bar input {
            width: 200px;
            font-weight: bold;
        }

        .navbar .search-bar button:hover {
            background-color: rgb(156, 167, 177);
        }

        .centered-heading {
            text-align: center;
            margin: 0;
            padding-bottom: 5px;
            margin-top: 100px;
        }

        .matches-table {
            width: 40%;
            border-collapse: collapse;
            margin: 0 auto;
            font-size: 18px;
            background-color: rgba(249, 234, 240, 0.9);
        }

        .matches-table a {
            text-decoration: none;
            color: black;
        }

        .matches-table th, .matches-table td {
            border: 1px solid rgb(243, 189, 189);
            padding: 10px;
            text-align: left;
        }

        .matches-table th {
            background-color: #efc9c9;
        }

        .search-bar button {
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            border: none;
        }

        .matches-table .heading {
            color: white;
        }

        /* Notification dot for pending friend requests */
        .friends-button {
            position: relative;
            display: inline-block;
        }

        .red-dot {
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
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
            <a href="friends.php" class="friends-button">
                Friends
                <?php if ($pending_count > 0): ?>
                    <span class="red-dot"></span>
                <?php endif; ?>
            </a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1>Welcome to Exchidea, <?php echo htmlspecialchars($user_name); ?>!</h1>
    <p>Your interests are: <strong><?php echo htmlspecialchars(implode(', ', $user_interests)); ?></strong></p>

    <h4 class="centered-heading">Matching Users:</h4>
    <?php if (!empty($matches)): ?>
        <table class="matches-table">
            <thead>
                <tr class="heading">
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
                            <a href="view_user.php?user_id=<?php echo $match['user_id']; ?>" title="Click on the name to view profile">
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
</body>
</html>