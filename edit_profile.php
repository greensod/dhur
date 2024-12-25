<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's email
$user_email = $_SESSION['user_email'];

// Fetch the user details
$query = "SELECT user_id, fname, email, mobile, dob, gender, interests FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);

    // Update query
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
</head>
<body>

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

    <div class="navigation">
        <a href="profile.php" class="btn">Back to Profile</a>
        <a href="home.php" class="btn">Home</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</div>

</body>
</html>
