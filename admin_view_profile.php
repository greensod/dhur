<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user_id is passed in the URL
if (!isset($_GET['user_id'])) {
    echo "Error: No user ID specified.";
    exit;
}

$user_id = $_GET['user_id'];

// Initialize variables to avoid warnings
$fname = $email = $profile_picture = $interests = "";
$skills_result = null;

// Fetch user details (fname, email, profile_picture, interests)
$query = "SELECT fname, email, profile_picture, interests FROM user WHERE user_id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Error preparing the query: ' . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fname, $email, $profile_picture, $interests);
$result = $stmt->fetch();

// Check if user details are fetched successfully
if (!$result) {
    echo "No user found with ID: " . htmlspecialchars($user_id);
    $fname = "Unknown";
    $email = "N/A";
    $interests = "N/A";
} else {
    $stmt->close(); // Close the statement
}

// Fetch and display skills
$skills_query = "SELECT skill_name, level, duration FROM user_skills WHERE user_id = ?";
$skills_stmt = $conn->prepare($skills_query);
if ($skills_stmt === false) {
    die('Error preparing skills query: ' . $conn->error);
}

$skills_stmt->bind_param("i", $user_id);
$skills_stmt->execute();
$skills_result = $skills_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        html, body {
            overflow-x: hidden;
            width: 100%;
        }

        h2 {
            color: #333;
            text-align: center;
        }
        h1{
            color: #333;
        }
        .profile-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .skills-table table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .skills-table th, .skills-table td {
            border: 1px solid rgb(161, 96, 97);
            padding: 12px;
            text-align: center;
        }
        .skills-table th {
            background-color:rgb(161, 96, 97);
        }
        .skills-table td {
            background-color: rgb(246, 215, 215);
        }
        .email-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color:rgb(161, 96, 97);
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .email-button:hover {
            background-color:rgb(204, 139, 139);
        }
        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color:rgb(161, 96, 97);
            text-align: center;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .back-button:hover {
            background-color:rgb(204, 139, 139);
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <?php if ($profile_picture): ?>
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <?php else: ?>
            <img src="images/default_profile_picture.jpg" alt="Default Profile Picture">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($fname); ?></h1>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Interests: <?php echo htmlspecialchars($interests); ?></p>
    </div>

    <?php if ($skills_result && $skills_result->num_rows > 0): ?>
        <div class="skills-table">
            <h2>Skills:</h2>
            <table>
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Level</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $skills_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['skill_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['level']); ?>/5</td>
                            <td><?php echo htmlspecialchars($row['duration']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No skills found for this user.</p>
    <?php endif; ?>

    <?php 
    // Encode the parameters for the Gmail link
    $subject = "Hello " . htmlspecialchars($fname);
    $body = "Hi " . htmlspecialchars($fname) . ",\n\nI wanted to reach out to you.\n\nBest regards,\nAdmin";
    $gmail_url = "https://mail.google.com/mail/?view=cm&fs=1&to=" . urlencode($email) . "&su=" . urlencode($subject) . "&body=" . urlencode($body);
    ?>
    <a href="<?php echo $gmail_url; ?>" target="_blank" class="email-button">Send Email</a>

    <a href="view_reports.php" class="back-button">Back to View Reports</a>
</div>

</body>
</html>