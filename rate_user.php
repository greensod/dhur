
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
    <style>
    
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8e8e9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        
        .container {
            background-color: #ffffff; 
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 400px;
        }

        
        h1 {
            text-align: center;
            color: rgb(202, 153, 178);
            margin-bottom: 20px;
        }

        
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }

        
        input[type="number"], textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: none;
        }

        input[type="number"] {
            text-align: center;
        }

        
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color:rgb(246, 196, 222); 
            color:  #a05268;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background-color:rgb(156, 167, 177);
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Rate User</h1>
    <form method="POST">
        <input type="hidden" name="friend_id" value="<?php echo $friend_id; ?>">
        <label for="rating">Rating (1 to 5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>
        <label for="review">Review:</label>
        <textarea id="review" name="review" rows="4" placeholder="Write your review here..."></textarea>
        <button type="submit">Submit Rating</button>
    </form>
</div>
</body>
</html>

