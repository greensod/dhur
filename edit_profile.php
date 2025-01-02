<?php
session_start();
include 'connection.php';


if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

$query = "SELECT user_id, fname, email, mobile, dob, gender, interests FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);

    $update_query = "UPDATE user SET fname='$name', email='$email', mobile='$mobile', dob='$dob', gender='$gender', interests='$interests' WHERE email='$user_email'";
    if (mysqli_query($conn, $update_query)) {
        header("Location: profile.php");
        exit;
    } else {
        $error_message = "Error updating profile information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Edit Profile</title>
    
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: rgb(245, 225, 225);
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column; /* Ensure the content follows the navbar */
        height: 100vh; /* Full height for the body */
    }

    .navbar {
        width: 100%;
        background-color: rgba(249, 234, 240, 0.9);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000; /* Keep navbar on top */
    }

    /* Left part of the navbar (logo) */
    .navbar .exchidea {
        font-size: 24px;
        font-weight: bold;
        color: rgb(230, 160, 192);
    }

    /* Right part of the navbar (buttons) */
    .navbar .nav-links {
        display: flex;
        gap: 15px;
        margin-right: 30px;
    }

    .navbar .nav-links a {
        text-decoration: none;
        padding: 6px 10px;
        background-color: rgb(230, 182, 206);
        color:  rgb(161, 96, 97);
        border-radius: 5px;
        font-weight: bold;
        font-size: 14px;
    }

    .navbar .nav-links a:hover {
        background-color: rgb(156, 167, 177);
    }

    .container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 10px; /* Compact padding */
        width: 90%;
        max-width: 500px;
        height: auto;
        box-sizing: border-box;
        margin-top: 80px; /* Space for the fixed navbar */
    }

    h1 {
        color: rgb(230, 160, 192);
        text-align: center;
        margin-bottom: 8px; /* Slightly reduced margin */
    }

    form label {
        font-weight: bold;
        display: block;
        color: #555;
        margin-bottom: 2px; /* Reduced gap between label and input */
    }

    form input, form select, form textarea {
        width: 100%;
        padding: 6px;
        margin: 0 0 8px; /* Reduced bottom margin to tighten spacing */
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 14px;
    }

    form textarea {
        resize: none; /* Prevent resizing */
    }

    form button {
        background-color: rgb(230, 160, 192);
        color:  rgb(161, 96, 97);
        border: none;
        padding: 8px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        font-weight: bold;
    }

    form button:hover {
        background-color: rgb(156, 167, 177);
    }

    p {
        text-align: center;
        margin-bottom: 8px; /* Slightly reduced margin */
    }
</style>


</head>
<body>

<div class="navbar">
    <span class="exchidea">EXCHIDEA</span>
    
    <div class="nav-links">
        <a href="profile.php">Profile</a>
        <a href="home.php">Home</a>          
    </div>
</div>

<div class="container">
    <h1>Edit Your Profile</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST">
        <label for="name">Name:</label><br>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['fname']); ?>"><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br>

        <label for="mobile">Mobile:</label><br>
        <input type="text" name="mobile" id="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>"><br>

        <label for="dob">Date of Birth:</label><br>
        <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($user['dob']); ?>"><br>

        <label for="gender">Gender:</label><br>
        <select name="gender" id="gender">
            <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
        </select><br>

        <label for="interests">Interests:</label><br>
        <textarea name="interests" id="interests" rows="4"><?php echo htmlspecialchars($user['interests']); ?></textarea><br>

        <button type="submit" name="update_profile" class="btn">Update Profile</button>
    </form>

</div>

</body>
</html>
