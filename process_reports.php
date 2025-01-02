<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_id = $_POST['report_id'];
    
    // Fetch the report details
    $query = "SELECT * FROM reports WHERE id = '$report_id'";
    $report_result = mysqli_query($conn, $query);
    $report = mysqli_fetch_assoc($report_result);
    
    // Ban the reported user if necessary
    $reported_user_id = $report['reported_user_id'];
    
    // Update user's banned status
    $ban_query = "UPDATE users SET is_banned = 1 WHERE id = '$reported_user_id'";
    mysqli_query($conn, $ban_query);

    // Mark the report as resolved
    $update_report_query = "UPDATE reports SET status = 'resolved' WHERE id = '$report_id'";
    mysqli_query($conn, $update_report_query);

    header("Location: admin.php?success=Report resolved and user banned");
    exit();
}
