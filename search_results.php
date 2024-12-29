<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$user_name = $_SESSION['user_name'];

// Check if the search form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_type = mysqli_real_escape_string($conn, $_POST['search_type']);
    $search_term = mysqli_real_escape_string($conn, $_POST['search_term']);

    if ($search_type === 'skill') {
        $query = "
            SELECT u.user_id, u.fname, us.skill_name, us.level, us.duration
            FROM user u
            JOIN user_skills us ON u.user_id = us.user_id
            WHERE u.email != '$user_email' AND us.skill_name LIKE '%$search_term%'
        ";
    } elseif ($search_type === 'interest') {
        $query = "
            SELECT u.user_id, u.fname, u.interests
            FROM user u
            WHERE u.email != '$user_email' AND FIND_IN_SET('$search_term', u.interests)
        ";
    } else {
        $message = "Invalid search type selected.";
    }

    if (isset($query)) {
        $result = mysqli_query($conn, $query);
        $matches = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $matches[] = $row;
            }
        } else {
            $message = "No matches found for your search.";
        }
    }
} else {
    $message = "Please use the search form to find matches.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .navbar {
            width: 100%;
            background-color: rgba(249, 234, 240, 0.9); 
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar .nav-links {
            display: flex;
            gap: 10px;
        }
        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
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
    </style>
</head>
<body>
    <div class="navbar">
        <span class="exchidea">EXCHIDEA</span>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="add_skills.php">Add Skills</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <h1>Search Results</h1>
    <?php if (isset($matches) && !empty($matches)): ?>
        <table class="matches-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <?php if ($search_type === 'skill'): ?>
                        <th>Skill</th>
                        <th>Level</th>
                        <th>Available Duration</th>
                    <?php elseif ($search_type === 'interest'): ?>
                        <th>Matching Interests</th>
                    <?php endif; ?>
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
                        <?php if ($search_type === 'skill'): ?>
                            <td><?php echo htmlspecialchars($match['skill_name']); ?></td>
                            <td><?php echo htmlspecialchars($match['level']); ?></td>
                            <td><?php echo htmlspecialchars($match['duration']); ?></td>
                        <?php elseif ($search_type === 'interest'): ?>
                            <td><?php echo htmlspecialchars($match['interests']); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
</body>
</html>
