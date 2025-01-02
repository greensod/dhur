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
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
    overflow: hidden; /* Prevent scrolling */
    height: 100vh; /* Make body take up full height */
    display: flex;
    justify-content: center;
    align-items: center;
}

html, body {
    width: 100%;
    overflow: hidden; /* Prevent horizontal scrolling */
}

h1, h2 {
    color: #333;
    text-align: center;
    margin: 0;
}

/* Profile container */
.profile-container {
    width: 100%;
    max-width: 600px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    overflow: hidden; /* Prevent scroll within container */
    height: 100%;
}

.profile-header img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 20px;
}

.skills-table table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    table-layout: fixed; /* Prevent overflow */
}

.skills-table th, .skills-table td {
    border: 1px solid rgb(161, 96, 97);
    padding: 12px;
    text-align: center;
}

.skills-table th {
    background-color: rgb(161, 96, 97);
}

.skills-table td {
    background-color: rgb(246, 215, 215);
}

.email-button, .back-button {
    display: block;
    margin: 20px auto;
    padding: 10px 20px;
    font-size: 16px;
    color: #fff;
    background-color: rgb(161, 96, 97);
    text-align: center;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    cursor: pointer;
}

.email-button:hover, .back-button:hover {
    background-color: rgb(204, 139, 139);
}

/* Make buttons responsive */
.email-button, .back-button {
    width: 80%; /* Ensure buttons are within the screen size */
    max-width: 300px; /* Limit maximum width */
}

/* Prevent overflow in smaller screens */
@media screen and (max-width: 768px) {
    .profile-container {
        padding: 15px; /* Reduce padding on smaller screens */
    }

    .skills-table td, .skills-table th {
        padding: 8px; /* Adjust table padding for smaller screens */
    }

    .profile-header img {
        width: 120px; /* Adjust image size */
        height: 120px; /* Adjust image size */
    }

    h1, h2 {
        font-size: 1.5em; /* Adjust font size */
    }

    .email-button, .back-button {
        width: 90%; /* Make buttons take up more width on smaller screens */
    }
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