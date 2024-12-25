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
$query = "SELECT fname, email, mobile, dob, gender, interests FROM user WHERE email != '$user_email'";
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
    <link rel="stylesheet" href="style.css">
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome in Exchidea, <?php echo htmlspecialchars($user_name); ?>!</h1>
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
                                <a href="profile.php?user_id=<?php echo $other_user['id']; ?>">
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

        <div class="navigation">
            <a href="profile.php" class="btn">Profile</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>
