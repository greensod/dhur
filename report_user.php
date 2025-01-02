<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['friend_id'])) {
    $reported_user_id = $_GET['friend_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['report_reason'])) {
        $report_reason = $_POST['report_reason'];
        $reporter_id = $_SESSION['user_id']; // Assume this is saved in the session
        
        // Insert the report into the database
        $query = "INSERT INTO reports (reported_user_id, reporter_id, report_reason, report_date) 
                  VALUES ('$reported_user_id', '$reporter_id', '$report_reason', NOW())";
        if (mysqli_query($conn, $query)) {
            echo "Report submitted successfully!";
        } else {
            echo "Error submitting report: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User</title>
</head>
<body>
    <h1>Report User</h1>
    <form method="POST">
        <label for="report_reason">Reason for Reporting:</label><br>
        <textarea id="report_reason" name="report_reason" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Submit Report</button>
    </form>
</body>
</html>
