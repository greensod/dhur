<?php
session_start();
include 'connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");  // Redirect to login page if not logged in
    exit();
}

// Fetch admin details if needed
$admin_id = $_SESSION['admin_id'];
$query = "SELECT email FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* Prevent horizontal scrolling */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .navbar {
            width: 100%;
            background-color: rgba(237, 215, 217, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(161, 96, 97);
        }

        /* Right part of the navbar (buttons) */
        .navbar .nav-links {
            display: flex;
            gap: 15px;
            margin-right: 30px;
        }

        .navbar .nav-links a {
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(161, 96, 97);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .navbar .nav-links a:hover {
            background-color: rgb(232, 154, 159);
        }

        /* Navbar search bar */
        .navbar .search-bar {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-grow: 1; /* Allow the search bar to grow and center */
            max-width: 400px; /* Max width for search bar */
            margin: 0 auto; /* Center the search bar */
        }

        /* Form elements in the search bar */
        .navbar .search-bar select,
        .navbar .search-bar input,
        .navbar .search-bar button {
            padding: 5px;
            font-size: 14px;
        }

        .navbar .search-bar input {
            width: 200px;
        }
        .navbar .search-bar button:hover {
            background-color: rgb(156, 167, 177);
        }

        .centered-heading {
            text-align: center; /* Centers the text horizontally */
            margin: 0; /* Remove any default margins */
            padding-bottom: 5px; /* Add some padding below the heading to control the space */
            margin-top: 100px; /* Add some margin at the top */
        }

        .matches-table {
            width: 40%;
            border-collapse: collapse;
            margin: 0 auto; /* Center the table horizontally */
            font-size: 18px;
            background-color: rgba(249, 234, 240, 0.9);
        }

        .matches-table a {
            text-decoration: none; /* Removes the underline */
            color: black;
        }

        .matches-table th, .matches-table td {
            border: 1px solid rgb(243, 189, 189);
            padding: 10px;
            text-align: left;
        }

        .matches-table th {
            background-color: #efc9c9;
        }
        
        .search-bar button {
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            border: none;
        }

        .matches-table .heading {
            color: white;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="exchidea">EXCHIDEA</div>
        <div class="nav-links">
            <a href="manage_users.php">Manage Users</a>
            <a href="view_reports.php">View Reports</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="centered-heading">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Hello, Admin! Your email: <?php echo htmlspecialchars($admin_email); ?></p>
    </div>

   
</body>
</html>