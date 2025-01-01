<?php
session_start();
include 'connection.php';
include 'friend_functions.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Validate and fetch the user_id from the query parameter
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$profile_user_id = $_GET['user_id'];

// Fetch logged-in user details
$user_email = $_SESSION['user_email'];
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$current_user = mysqli_fetch_assoc($result);
$current_user_id = $current_user['user_id'];

// Fetch profile user details
$query = "SELECT fname, interests, profile_picture FROM user WHERE user_id = $profile_user_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User not found.");
}

$profile_user = mysqli_fetch_assoc($result);

// Check if a friend request has already been sent or they are already friends
$request_query = "SELECT * FROM friend_requests WHERE sender_id = $current_user_id AND receiver_id = $profile_user_id";
$request_result = mysqli_query($conn, $request_query);
$friend_query = "
    SELECT * FROM friends 
    WHERE (user1_id = $current_user_id AND user2_id = $profile_user_id) 
       OR (user1_id = $profile_user_id AND user2_id = $current_user_id)";
$friend_result = mysqli_query($conn, $friend_query);

$request_sent = mysqli_num_rows($request_result) > 0;
$is_friend = mysqli_num_rows($friend_result) > 0;

// Fetch user skills
$query_skills = "SELECT skill_name FROM user_skills WHERE user_id = $profile_user_id";
$result_skills = mysqli_query($conn, $query_skills);

$skills = [];
while ($row = mysqli_fetch_assoc($result_skills)) {
    $skills[] = $row['skill_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color:rgb(237, 207, 222);
    }
    .container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .profile-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
    }
    h1 {
        color: #333;
        font-size: 24px;
        margin-bottom: 10px;
    }
    p {
        font-size: 16px;
        color: #555;
        margin-bottom: 10px;
    }
    .skills-list {
        margin-top: 20px;
        text-align: left;
        padding-left: 20px;
    }
    .skills-list h3 {
        font-size: 18px;
        color: #333;
        margin-bottom: 10px;
    }
    .skills-list li {
        font-size: 16px;
        color: #555;
    }
    form {
        margin-top: 20px;
    }
    button {
        background-color:rgb(240, 164, 201);
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
    }
    button:hover {
        background-color:rgb(156, 167, 177);
    }
    .back-link {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color:rgb(240, 164, 201);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
    .back-link:hover {
        background-color:rgb(156, 167, 177);
    }
    .message {
        font-size: 16px;
        color: #007bff;
        margin-top: 20px;
    }
    </style>

</head>
<body>
    <div class="container">
        <?php if (!empty($profile_user['profile_picture'])): ?>
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($profile_user['profile_picture']); ?>" 
                 alt="Profile Picture" class="profile-picture">
        <?php else: ?>
            <img src="uploads/profile_pictures/default.png" 
                 alt="Default Profile Picture" class="profile-picture">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($profile_user['fname']); ?></h1>
        <p><strong>Interests:</strong> <?php echo htmlspecialchars($profile_user['interests']); ?></p>

        <?php if (!empty($skills)): ?>
            <div class="skills-list">
                <h3>Skills:</h3>
                <ul>
                    <?php foreach ($skills as $skill): ?>
                        <li><?php echo htmlspecialchars($skill); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p>No skills listed.</p>
        <?php endif; ?>

        <?php if ($profile_user_id != $current_user_id): ?>
            <?php if ($is_friend): ?>
                <p>You are already friends with this user.</p>
            <?php elseif ($request_sent): ?>
                <p>Friend request already sent.</p>
            <?php else: ?>
                <form method="POST" action="send_request.php">
                    <input type="hidden" name="receiver_id" value="<?php echo $profile_user_id; ?>">
                    <button type="submit">Add Friend</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <a href="home.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>
