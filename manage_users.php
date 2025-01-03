<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        body {
            background-color: rgba(249, 234, 240, 0.9);
            padding: 0 10px;
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

        .container {
            max-width: 500px;
            width: 100%;
            background-color: white;
            margin: 20px auto;
            padding: 30px;
            box-shadow: 10px 10px 10px rgba(72, 71, 71, 0.1);
            margin-top: 90px;
            border-radius: 20px;
        }
        .container .title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            color:  rgb(161, 96, 97);
            text-transform: uppercase;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .users-table th, .users-table td {
            border: 1px solid  rgb(242, 178, 179);
            padding: 10px;
            text-align: left;
            font-size: 15px;
        }
        .users-table th {
            background-color: rgb(161, 96, 97);
            color: white;
        }
        .users-table td {
            background-color: rgba(230, 158, 195, 0.1);
        }
        .btn {
            width: 100%;
            padding: 8px 10px;
            border: none;
            background:  rgb(161, 96, 97);
            font-size: 15px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
            margin-bottom: 6px;
        }
        .btn:hover {
            background: rgb(156, 167, 177);
        }
        @media (max-width: 420px) {
            .container {
                padding: 20px;
            }
            .container .title {
                font-size: 20px;
            }
            .users-table th, .users-table td {
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="exchidea">EXCHIDEA</div>
        <div class="nav-links">
            <a href="admin.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <div class="title">Manage Users</div>

        <!-- Display success or failure message -->
        <?php
        if (isset($_GET['ban']) && $_GET['ban'] == 'success') {
            echo '<p style="color: green; text-align: center;">Ban successful!</p>';
        } elseif (isset($_GET['ban']) && $_GET['ban'] == 'failed') {
            echo '<p style="color: red; text-align: center;">Failed to ban the user, please try again.</p>';
        }
        ?>

        <table class="users-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start();
                include 'connection.php';

                if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
                    header("Location: admin_login.php");
                    exit();
                }

                $query = "SELECT DISTINCT u.user_id, u.email FROM user u JOIN reports r ON u.user_id = r.reported_user_id";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $stmt->bind_result($user_id, $user_email);

                while ($stmt->fetch()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user_id); ?></td>
                        <td><?php echo htmlspecialchars($user_email); ?></td>
                        <td>
                            <form action="ban_user.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                                <button class="btn" type="submit">Ban</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php $stmt->close(); ?>
            </tbody>
        </table>
    </div>
</body>
</html>