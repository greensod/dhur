<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Check for a valid user_id parameter in the URL
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$profile_user_id = $_GET['user_id'];

// Fetch profile user's details (name)
$query = "SELECT fname FROM user WHERE user_id = $profile_user_id";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$profile_user = mysqli_fetch_assoc($result);

if (!$profile_user) {
    die("User not found.");
}

// Fetch reviews and ratings for the profile user from the `ratings` table
$query_reviews = "
    SELECT r.rating, r.review, u.fname AS reviewer_name 
    FROM ratings r 
    JOIN user u ON r.rater_user_id = u.user_id 
    WHERE r.rated_user_id = $profile_user_id";
$result_reviews = mysqli_query($conn, $query_reviews);

// Check for query errors
if (!$result_reviews) {
    die("Database query failed: " . mysqli_error($conn));
}

$reviews = [];
if (mysqli_num_rows($result_reviews) > 0) {
    while ($row = mysqli_fetch_assoc($result_reviews)) {
        $reviews[] = $row;
    }
} else {
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews for <?php echo htmlspecialchars($profile_user['fname']); ?></title>
    <style>/* Basic styling for the container */
.container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: rgba(249, 234, 240, 0.9);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Header styling */
h1 {
    font-size: 24px;
    text-align: center;
    color: rgb(161, 96, 97);
    margin-bottom: 20px;
}

/* Styling for the review items */
.review-item {
    background-color: rgba(249, 234, 240, 0.9);
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Reviewer name and rating */
.reviewer-name {
    font-weight: bold;
    color: rgb(161, 96, 97);
}

.rating {
    font-style: italic;
    color: rgb(156, 167, 177);
}

/* Review text styling */
.review-text {
    margin-top: 10px;
    font-size: 16px;
    color: rgb(90, 90, 90);
}

/* Back to Profile link styling */
.back-link {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 15px;
    background-color: rgb(230, 182, 206);
    color: rgb(161, 96, 97);
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.back-link:hover {
    background-color: rgb(156, 167, 177);
}

/* Responsive adjustments for smaller screens */
@media (max-width: 600px) {
    .container {
        padding: 15px;
    }

    h1 {
        font-size: 20px;
    }

    .review-item {
        padding: 10px;
    }

    .back-link {
        padding: 8px 12px;
        font-size: 14px;
    }
}
</style>
</head>
<body>
    <div class="container">
        <h1>Reviews for <?php echo htmlspecialchars($profile_user['fname']); ?></h1>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <span class="reviewer-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></span>
                    <span class="rating"> - Rating: <?php echo htmlspecialchars($review['rating']); ?> / 5</span>
                    <p class="review-text"><?php echo htmlspecialchars($review['review']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet.</p>
        <?php endif; ?>

        <a href="view_user.php?user_id=<?php echo $profile_user_id; ?>" class="back-link">Back to Profile</a>
    </div>
</body>
</html>
