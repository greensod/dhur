<?php
include 'connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Delete user from the database
    $query = "DELETE FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Debug statement before redirect
            echo "User deleted successfully. Redirecting...";
            header("Location: manage_users.php");
            exit;
        } else {
            echo "Failed to delete the user. Please try again.";
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
