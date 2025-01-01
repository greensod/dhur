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

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $friend_id = $_POST['friend_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'] ?? null;

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
