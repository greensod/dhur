<?php
include 'connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Mark user as banned
    $query = "UPDATE user SET is_banned = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Redirect with success message
            header("Location: manage_users.php?ban=success");
            $stmt->close();
            exit;
        } else {
            // Redirect with failure message
            header("Location: manage_users.php?ban=failed");
            $stmt->close();
            exit;
        }
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
