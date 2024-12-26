<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Fetch user details (assuming they were stored in the session)
$user_name = $_SESSION['user_name']; // You would store the name during login
$query = "SELECT user_id,fname, email, mobile, dob, gender, interests FROM user WHERE email != '$user_email'";
$result = mysqli_query($conn, $query);

$users = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row; // Add each user to the $users array
    }
} else {
    $message = "No other users found."; // Save the message to display later
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Home</title>
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
            background-color: lightblue;
        }

        .user-table th, .user-table td {
            border: 1px solid #ecbfbf;
            padding: 8px;
        }

        .user-table th {
            background-color: #efc9c9;
        }
        .navbar {
            
            width: 100%;
            background-color: rgba(178, 235, 221, 0.9); /* Slightly transparent background */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            z-index: 1000; /* Ensures navbar stays above other content */
            box-sizing: border-box;
    
        }

        .navbar .nav-links {
            display: flex;
            gap: 7px;
            margin-left: auto; 
        }
        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
            margin-right: 20px; /* Minimum gap */
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

        .navbar .nav-links a:hover {
            background-color: rgb(130, 242, 227);
        }
        .welcome {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
            margin-right: 20px; /* Minimum gap */
            margin-top: 80px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
         <span class="exchidea">EXCHIDEA</span>
         <div class="nav-links">
            <a href="profile.php" class="btn">Profile</a>
            <a href="logout.php" class="btn">Logout</a>
         </div>
        </div>
        <h1 class="welcome">Welcome to Exchidea, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>You have successfully logged in.</p>

        <h2>Other Registered Users:</h2>
        <?php if (!empty($users)): ?>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Interests</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $other_user): ?>
                        <!-- <tr>
                            <td><?php echo htmlspecialchars($other_user['fname']); ?></td>
                            <td><?php echo htmlspecialchars($other_user['interests']); ?></td>
                        </tr> -->
                        <tr>
                            <!-- Each name links to the profile.php of the respective user -->
                            <td>
                               <a href="view_user.php?user_id=<?php echo $other_user['user_id']; ?>">
                                    <?php echo htmlspecialchars($other_user['fname']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($other_user['interests']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No other users found.</p>
        <?php endif; ?>

        
    </div>
</body>
</html>
