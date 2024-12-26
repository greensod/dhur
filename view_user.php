<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Validate and fetch the user_id from the query parameter
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = $_GET['user_id'];

// Debugging: Output the user_id to check if it's correct
// echo "User ID: " . $user_id . "<br>"; 

// Fetch user details based on user_id
$query = "SELECT fname, interests, profile_picture FROM user WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);

// Debugging: Check if query executed correctly
if (!$result) {
    die("Error with query: " . mysqli_error($conn));
}

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    die("User not found.");
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
            background-color: #f4f4f9;
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
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: rgb(230, 182, 206);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: rgb(130, 242, 227);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($user['profile_picture'])): ?>
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
                 alt="Profile Picture" class="profile-picture">
        <?php else: ?>
            <img src="uploads/profile_pictures/default.png" 
                 alt="Default Profile Picture" class="profile-picture">
        <?php endif; ?>
        <h1><?php echo htmlspecialchars($user['fname']); ?></h1>
        <p><strong>Interests:</strong> <?php echo htmlspecialchars($user['interests']); ?></p>
        <a href="home.php" class="back-link">Back to Home</a>
    </div>
</body>
</html>
