<!DOCTYPE html>
<html lang="en"></html>

<?php
session_start();
include 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and the user_id session variable is set
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Check if user_id is not set in the session
if (!isset($_SESSION['user_id'])) {
    // Get the user_id from the database if not set in the session
    $user_email = $_SESSION['user_email'];
    $query = "SELECT user_id FROM user WHERE email = ?";  // Corrected table name here
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $_SESSION['user_id'] = $user_id; // Store user_id in session
        }
        $stmt->close();
    }
}

// Get the reported_user_id from the URL
if (isset($_GET['friend_id'])) {
    $reported_user_id = $_GET['friend_id'];
} else {
    $reported_user_id = null;
}

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['report_reason'])) {
        $report_reason = $_POST['report_reason'];
        $reporter_id = $_SESSION['user_id']; // Assume this is now set in the session

        // Check if reported_user_id is valid (not null)
        if (empty($reported_user_id)) {
            $message = "Error: No user selected for reporting.";
        } else {
            // Insert the report into the database
            $query = "INSERT INTO reports (reported_user_id, reporter_user_id, reason, created_at) 
                      VALUES (?, ?, ?, NOW())";
            if ($stmt = $conn->prepare($query)) {
                // Bind the parameters and execute the statement
                $stmt->bind_param("iis", $reported_user_id, $reporter_id, $report_reason);
                if ($stmt->execute()) {
                    // Redirect after successful report submission
                    header("Location: report_user.php?success=1");
                    exit; // Stop further script execution
                } else {
                    $message = "Error submitting report: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Error preparing the query: " . $conn->error;
            }
        }
    }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User</title>
    <style>
        /* General body styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8e8e9; /* Dusky rose pink */
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Centered container for the form */
        .container {
            background-color: #ffffff; /* White card-style background */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        /* Heading styling */
        h1 {
            color: #a05268; /* Darker dusky pink */
            margin-bottom: 20px;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Label styling */
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
        }

        /* Input and textarea styling */
        input[type="number"], textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: none;
            font-size: 14px;
        }

        /* Textarea specific styling */
        textarea {
            min-height: 120px;
            font-size: 16px;
        }

        /* Button styling */
        button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #a05268; /* Dusky rose pink */
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #903f57; /* Darker shade for hover */
            transform: translateY(-2px); /* Small hover effect */
        }

        /* Success or error message styling */
        p {
            text-align: center;
            font-size: 16px;
            color: #a05268;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            textarea {
                font-size: 14px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Report User</h1>
        
        <!-- Display success or error message -->
        <?php 
        if (isset($message)) { 
            echo "<p>$message</p>"; 
        }
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<p>Report submitted successfully!</p>";
        }
        ?>

        <form method="POST">
            <label for="report_reason">Reason for Reporting:</label><br>
            <textarea id="report_reason" name="report_reason" rows="4" cols="50" required></textarea><br><br>
            <button type="submit">Submit Report</button>
        </form>
    </div>
</body>
</html>
