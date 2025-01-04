<?php
session_start();
include 'connection.php';


ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}


if (!isset($_SESSION['user_id'])) {
    
    $user_email = $_SESSION['user_email'];
    $query = "SELECT user_id FROM user WHERE email = ?";  
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $_SESSION['user_id'] = $user_id;
        }
        $stmt->close();
    }
}


if (isset($_GET['friend_id'])) {
    $reported_user_id = $_GET['friend_id'];
} else {
    $reported_user_id = null;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['report_reason'])) {
        $report_reason = $_POST['report_reason'];
        $reporter_id = $_SESSION['user_id']; 

        
        if (empty($reported_user_id)) {
            $message = "Error: No user selected for reporting.";
        } else {
            
            $query = "INSERT INTO reports (reported_user_id, reporter_user_id, reason, created_at) 
                      VALUES (?, ?, ?, NOW())";
            if ($stmt = $conn->prepare($query)) {
                
                $stmt->bind_param("iis", $reported_user_id, $reporter_id, $report_reason);
                if ($stmt->execute()) {
                    
                    header("Location: report_user.php?success=1");
                    exit; 
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report User</title>
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
            padding: 30px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%; 
            background-color: #f7d9dc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000; 
        }

        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: #e892a3;
            text-decoration: none;
        }

        .navbar .nav-links {
            display: flex;
            gap: 10px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            padding: 8px 15px;
            background-color:rgb(197, 149, 154);
            color: #933a4d;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-right: 35px;
        }

        .navbar .nav-links a:hover {
            background-color:rgb(176, 194, 215);
        }
        h1 {
            color:rgb(223, 161, 193);
            margin-bottom: 20px;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
        }

       
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

        
        textarea {
            min-height: 120px;
            font-size: 16px;
        }

        
        button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color:rgb(246, 196, 222); 
            color: #a05268;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            font-weight: bold;
        }

        button:hover {
            background-color:rgb(156, 167, 177); 
            transform: translateY(-2px);
        }

        
        p {
            text-align: center;
            font-size: 16px;
            color: #a05268;
            font-weight: bold;
        }

        
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
    <div class="navbar">
            <a href="home.php" class="exchidea">EXCHIDEA</a>
            <div class="nav-links">
                <a href="friends.php">Friends</a>
                <a href="home.php">Home</a>
            </div>
    </div>
    <div class="container">
        <h1>Report User</h1>
        
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