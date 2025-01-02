<?php
session_start();
include 'connection.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");  // Redirect to login page if not logged in
    exit();
}

// Fetch all reports from the reports table
$query = "SELECT r.id, u1.email AS reporter_email, u2.email AS reported_email, r.reason, r.created_at 
          FROM reports r
          JOIN user u1 ON r.reporter_user_id = u1.user_id
          JOIN user u2 ON r.reported_user_id = u2.user_id
          ORDER BY r.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$stmt->bind_result($report_id, $reporter_email, $reported_email, $reason, $created_at);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .navbar {
            width: 100%;
            background-color: rgba(249, 234, 240, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
        }

        .navbar .nav-links a {
            text-decoration: none;
            padding: 6px 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
        }

        .navbar .nav-links a:hover {
            background-color: rgb(156, 167, 177);
        }

        .reports-table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            font-size: 16px;
            background-color: rgba(249, 234, 240, 0.9);
        }

        .reports-table th, .reports-table td {
            border: 1px solid rgb(243, 189, 189);
            padding: 10px;
            text-align: left;
        }

        .reports-table th {
            background-color: #efc9c9;
        }

        .reports-table td {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="exchidea">Admin Dashboard</div>
        <div class="nav-links">
            <a href="manage_users.php">Manage Users</a>
            <a href="view_reports.php">View Reports</a>
            <a href="admin_settings.php">Settings</a>
        </div>
    </div>

    <h1 style="text-align: center; margin-top: 30px;">Reported Issues</h1>

    <table class="reports-table">
        <thead>
            <tr>
                <th>Report ID</th>
                <th>Reporter Email</th>
                <th>Reported Email</th>
                <th>Reason</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($stmt->fetch()): ?>
            <tr>
                <td><?php echo htmlspecialchars($report_id); ?></td>
                <td><?php echo htmlspecialchars($reporter_email); ?></td>
                <td><?php echo htmlspecialchars($reported_email); ?></td>
                <td><?php echo htmlspecialchars($reason); ?></td>
                <td><?php echo htmlspecialchars($created_at); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="admin.php">
            <button>Back to Dashboard</button>
        </a>
    </div>

</body>
</html>

<?php
$stmt->close();
?>
