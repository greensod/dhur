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

// Get the friend's ID from the URL
if (isset($_GET['friend_id'])) {
    $friend_id = $_GET['friend_id'];
} else {
    die("Error: No friend specified.");
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $friend_id = $_POST['friend_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'] ?? null;

    // Sanitize the inputs to prevent SQL injection
    $rating = mysqli_real_escape_string($conn, $rating);
    $review = mysqli_real_escape_string($conn, $review);

    // Insert the rating into the database
    $query = "INSERT INTO ratings (rater_user_id, rated_user_id, rating, review) 
              VALUES ($current_user_id, $friend_id, $rating, '$review') 
              ON DUPLICATE KEY UPDATE rating = $rating, review = '$review'";

    if (mysqli_query($conn, $query)) {
        echo "Rating submitted successfully.";
        header("Location: view_user.php?user_id=$friend_id");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate User</title>
</head>
<body>

<h1>Rate User</h1>

<form method="POST">
    <input type="hidden" name="friend_id" value="<?php echo $friend_id; ?>">
    <label for="rating">Rating (1 to 5):</label>
    <input type="number" id="rating" name="rating" min="1" max="5" required>
    <br><br>
    <label for="review">Review:</label>
    <textarea id="review" name="review" rows="4" cols="50"></textarea>
    <br><br>
    <button type="submit">Submit Rating</button>
</form>

</body>
</html>
