<?php 
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's ID based on their email
$user_email = $_SESSION['user_email'];
$query = "SELECT user_id FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$current_user = mysqli_fetch_assoc($result);
$current_user_id = $current_user['user_id'];

// Check for a valid profile user ID
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$profile_user_id = $_GET['user_id'];

// Fetch profile user's details (name, interests, profile picture)
$query = "SELECT fname, interests, profile_picture FROM user WHERE user_id = $profile_user_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    die("User not found.");
}

$profile_user = mysqli_fetch_assoc($result);

// Check if a friend request has been sent by the current user to the profile user
$request_query = "SELECT * FROM friend_requests WHERE sender_id = $current_user_id AND receiver_id = $profile_user_id AND status = 'pending'";
$request_result = mysqli_query($conn, $request_query);

// Check if a friend request has been sent by the profile user to the current user
$received_request_query = "SELECT * FROM friend_requests WHERE sender_id = $profile_user_id AND receiver_id = $current_user_id AND status = 'pending'";
$received_request_result = mysqli_query($conn, $received_request_query);

// Check if the two users are already friends
$friend_query = "
    SELECT * FROM friends 
    WHERE (user1_id = $current_user_id AND user2_id = $profile_user_id) 
       OR (user1_id = $profile_user_id AND user2_id = $current_user_id)";
$friend_result = mysqli_query($conn, $friend_query);

$request_sent = mysqli_num_rows($request_result) > 0;
$received_request = mysqli_num_rows($received_request_result) > 0;
$is_friend = mysqli_num_rows($friend_result) > 0;

// Fetch profile user's skills
$query_skills = "SELECT skill_name, level, duration FROM user_skills WHERE user_id = $profile_user_id";
$result_skills = mysqli_query($conn, $query_skills);

$skills = [];
while ($row = mysqli_fetch_assoc($result_skills)) {
    $skills[] = $row;
}

// Fetch profile user's rating average
$query_rating = "SELECT AVG(rating) as average_rating FROM ratings WHERE rated_user_id = $profile_user_id";
$rating_result = mysqli_query($conn, $query_rating);
$rating = mysqli_fetch_assoc($rating_result);
$average_rating = $rating['average_rating'] ? round($rating['average_rating'], 2) : "No ratings yet";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(237, 207, 222);
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
        .profile-info {
            flex-grow: 1;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin: 0;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .add-friend {
            margin-left: auto;
        }
        button {
            background-color: rgb(240, 164, 201);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgb(156, 167, 177);
        }
        .skills-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .skills-table th, .skills-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .skills-table th {
            background-color: rgb(230, 182, 206);
            color: rgb(161, 96, 97);
        }
        .rating-summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9e6f1;
            border-radius: 10px;
            border: 1px solid #ff99cc;
        }
        .review-form {
            margin-top: 20px;
        }
        .review-form .btn {
            color: rgb(161, 96, 97);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: rgb(161, 96, 97);
            text-decoration: none;
            font-size: 16px;
            background-color:  rgb(230, 182, 206);
            padding: 10px 20px;
            border-radius: 5px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <?php if (!empty($profile_user['profile_picture'])): ?>
                <img src="uploads/profile_pictures/<?php echo htmlspecialchars($profile_user['profile_picture']); ?>" 
                     alt="Profile Picture" class="profile-picture">
            <?php else: ?>
                <img src="images/default_profile_picture.jpg" 
                     alt="Default Profile Picture" class="profile-picture">
            <?php endif; ?>

            <div class="profile-info">
                <h1><?php echo htmlspecialchars($profile_user['fname']); ?></h1>
                <p><strong>Interests:</strong> <?php echo htmlspecialchars($profile_user['interests']); ?></p>
            </div>

            <?php if ($profile_user_id != $current_user_id): ?>
                <div class="add-friend">
                    <?php if ($is_friend): ?>
                        <button disabled>You are Friends</button>
                    <?php elseif ($request_sent): ?>
                        <form method="POST" action="cancel_request.php">
                            <input type="hidden" name="receiver_id" value="<?php echo $profile_user_id; ?>">
                            <button type="submit">Cancel Request</button>
                        </form>
                    <?php elseif ($received_request): ?>
                        <form method="POST" action="accept_request.php">
                            <input type="hidden" name="sender_id" value="<?php echo $profile_user_id; ?>">
                            <button type="submit">Accept Request</button>
                        </form>
                        <form method="POST" action="reject_request.php">
                            <input type="hidden" name="sender_id" value="<?php echo $profile_user_id; ?>">
                            <button type="submit">Reject Request</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="send_request.php">
                            <input type="hidden" name="receiver_id" value="<?php echo $profile_user_id; ?>">
                            <button type="submit">Add Friend</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($skills)): ?>
            <table class="skills-table">
                <thead>
                    <tr>
                        <th>Skill Name</th>
                        <th>Level</th>
                        <th>Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($skill['skill_name']); ?></td>
                            <td><?php echo htmlspecialchars($skill['level']); ?>/5</td>
                            <td><?php echo htmlspecialchars($skill['duration']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No skills listed.</p>
        <?php endif; ?>

        <div class="rating-summary">
            <p><strong>Average Rating:</strong> <?php echo $average_rating; ?> / 5</p>
        </div>

        <form action="review_page.php" method="get" class="review-form">
            <input type="hidden" name="user_id" value="<?php echo $profile_user_id; ?>">
            <button type="submit" class="btn">View Reviews</button>
        </form>

        <a href="home.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>