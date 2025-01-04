<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];


function countPendingFriendRequests($user_id) {
    global $conn;
    $query = "SELECT COUNT(*) AS pending_count FROM friend_requests WHERE receiver_id = '$user_id' AND status = 'pending'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['pending_count'];
}


$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$current_user_id = $user['user_id'];


$pending_count = countPendingFriendRequests($current_user_id);


$query = "SELECT interests FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$user_interests = $user ? explode(',', $user['interests']) : [];


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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9eaf0;
            color: #4a4a4a;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .navbar {
            width: 100%;
            background-color: #e6a0c0;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar .exchidea {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }

        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            color: #fff;
            padding: 8px 16px;
            background-color: #e09da8;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .navbar .nav-links a:hover {
            background-color: #a3acb1;
        }

        .navbar .search-bar {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-grow: 1;
            max-width: 450px;
        }

        .navbar .search-bar select,
        .navbar .search-bar input,
        .navbar .search-bar button {
            padding: 10px;
            font-size: 14px;
            color: #4a4a4a;
            font-weight: 600;
            border: 1px solid #e09da8;
            border-radius: 5px;
        }

        .navbar .search-bar input {
            width: 250px;
        }

        .navbar .search-bar button {
            background-color: #e09da8;
            color: #fff;
            cursor: pointer;
        }

        .navbar .search-bar button:hover {
            background-color: #a3acb1;
        }

        .centered-heading {
            text-align: center;
            font-size: 24px;
            margin-top: 80px;
            color: #4a4a4a;
        }

        .matches-table {
            width: 60%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #f5d0c7;
        }

        .matches-table th,
        .matches-table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #f1a7b8;
        }

        .matches-table th {
            background-color: #f0c0c6;
            color: #4a4a4a;
        }

        .matches-table tr:nth-child(even) {
            background-color: #f9eaf0;
        }

        .matches-table a {
            text-decoration: none;
            color: #4a4a4a;
        }

        .matches-table a:hover {
            text-decoration: underline;
        }

        .matches-table .heading {
            color: #4a4a4a;
        }

        .friends-button {
            position: relative;
            display: inline-block;
        }

        .red-dot {
            width: 12px;
            height: 12px;
            background-color: red;
            border-radius: 50%;
            position: absolute;
            top: -5px;
            right: -5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            font-size: 36px;
            margin-top: 40px;
            color: #4a4a4a;
        }

        p {
            text-align: center;
            font-size: 18px;
            color: #6c6c6c;
        }

        strong {
            color: #e09da8;
        }
        
        
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php" class="exchidea">EXCHIDEA</a>

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