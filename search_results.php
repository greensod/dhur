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
            color: rgb(161, 96, 97);
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .navbar .nav-links a:hover {
            background-color: rgb(156, 167, 177);
        }
        .matches-table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
            font-size: 18px;
            background-color: rgba(249, 234, 240, 0.9);
            margin-top: 60px;
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
            background-color:rgb(241, 192, 192);
        }
        .matches-table .heading {
            color: rgb(161, 96, 97);
        }
        .search-bar button{
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: rgb(161, 96, 97);
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            border: none;
        }
        .navbar .search-bar select,
        .navbar .search-bar input,
        .navbar .search-bar button {
            padding: 5px;
            font-size: 14px;
            color: rgb(161, 96, 97);
            font-weight: bold;

        }
        .view-btn {
            display: inline-block;
            padding: 4px 8px; 
            background-color:  rgb(230, 182, 206); 
            color: rgb(161, 96, 97) !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px; 
            text-align: center;
        }

        .view-btn:hover {
            background-color:  rgb(156, 167, 177);
            cursor: pointer;
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
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="add_skills.php">Add Skills</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- <h1>Search Results</h1> -->
<?php if (isset($matches) && !empty($matches)): ?>
    <table class="matches-table">
        <thead>
            <tr class="heading">
                <th>Name</th>
                <?php if ($search_type === 'skill'): ?>
                    <th>Skill</th>
                    <th>Level</th>
                    <th>Available Duration</th>
                <?php elseif ($search_type === 'interest'): ?>
                    <th>Matching Interests</th>
                <?php endif; ?>
                <th>Profile</th> <!-- Changed to "Actions" -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match): ?>
                <tr>
                    <td><?php echo htmlspecialchars($match['fname']); ?></td> <!-- Plain name without the link -->
                    <?php if ($search_type === 'skill'): ?>
                        <td><?php echo htmlspecialchars($match['skill_name']); ?></td>
                        <td><?php echo htmlspecialchars($match['level']); ?></td>
                        <td><?php echo htmlspecialchars($match['duration']); ?></td>
                    <?php elseif ($search_type === 'interest'): ?>
                        <td><?php echo htmlspecialchars($match['interests']); ?></td>
                    <?php endif; ?>
                    <!-- View User button -->
                    <td>
                        <a href="view_user.php?user_id=<?php echo $match['user_id']; ?>" class="view-btn">View User</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

</body>
</html>
