<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Get the user ID
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$user_id = $user['user_id'];

// Fetch user skills
$skills_query = "SELECT skill_id, skill_name, level, duration FROM user_skills WHERE user_id = $user_id";
$skills_result = mysqli_query($conn, $skills_query);
$skills = [];
while ($row = mysqli_fetch_assoc($skills_result)) {
    $skills[] = $row;
}

// Add new skill
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_skill'])) {
    $skill_name = mysqli_real_escape_string($conn, $_POST['skill_name']);
    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    if (!empty($skill_name) && !empty($level) && !empty($duration)) {
        $insert_query = "INSERT INTO user_skills (user_id, skill_name, level, duration) 
                         VALUES ($user_id, '$skill_name', '$level', '$duration')";
        if (mysqli_query($conn, $insert_query)) {
            header("Location: add_skills.php"); // Reload the page to show new skill
            exit;
        } else {
            $error_message = "Error adding skill: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Remove skill
if (isset($_GET['remove_skill_id'])) {
    $skill_id = $_GET['remove_skill_id'];
    $delete_query = "DELETE FROM user_skills WHERE skill_id = $skill_id AND user_id = $user_id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: add_skills.php"); // Reload the page to reflect removed skill
        exit;
    } else {
        $error_message = "Error removing skill: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Skills</title>
    <style>
        /* Add some styles for form layout */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: rgb(230, 182, 206);
            color: rgb(161, 96, 97);
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .form-container button:hover {
            background-color:rgb(156, 167, 177);
        }
        .message {
            text-align: center;
            margin: 10px 0;
        }
        .skill-list {
            margin-top: 20px;
        }
        .skill-list ul {
            list-style: none;
            padding: 0;
        }
        .skill-list li {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            margin-bottom: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .skill-list button {
            background-color:rgb(198, 27, 101);
            color: white;
            border: none;
            cursor: pointer;
        }
        .skill-list button:hover {
            background-color:rgb(198, 27, 101);
        }
        /* Style for the Back to Profile button */
        .back-to-profile {
            display: inline-block;
            padding: 10px 20px;
            margin-bottom: 20px;
            background-color: rgb(230, 182, 206);
            color: rgb(161, 96, 97);
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
        .back-to-profile:hover {
            background-color: rgb(156, 167, 177);
        }
    </style>
</head>
<body>

    <!-- Back to Profile Button -->
    <a href="profile.php" class="back-to-profile">Back to Profile</a>

    <div class="form-container">
        <h2>Add Skill</h2>
        <?php if (isset($error_message)): ?>
            <p class="message" style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <label for="skill_name">Skill Name:</label>
            <input type="text" id="skill_name" name="skill_name" required>

            <label for="level">Skill Level (1-5):</label>
            <select id="level" name="level" required>
                <option value="1">1 - Beginner</option>
                <option value="2">2 - Basic</option>
                <option value="3">3 - Intermediate</option>
                <option value="4">4 - Advanced</option>
                <option value="5">5 - Expert</option>
            </select>

            <label for="duration">Available Duration:</label>
            <input type="text" id="duration" name="duration" placeholder="e.g., Weekends or Weekdays 6-8 PM" required>

            <button type="submit" name="add_skill">Add Skill</button>
        </form>
    </div>

    <div class="skill-list">
        <h3>Your Skills:</h3>
        <?php if (!empty($skills)): ?>
            <ul>
                <?php foreach ($skills as $skill): ?>
                    <li>
                        <span><?php echo htmlspecialchars($skill['skill_name']); ?> - Level <?php echo htmlspecialchars($skill['level']); ?>/5</span>
                        <span>Duration: <?php echo htmlspecialchars($skill['duration']); ?></span>
                        <a href="add_skills.php?remove_skill_id=<?php echo $skill['skill_id']; ?>" onclick="return confirm('Are you sure you want to remove this skill?');">
                            <button>Remove</button>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No skills added yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>
