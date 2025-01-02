<?php include 'connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <title>Login</title>
</head>
<body>
    <div class="container">
        <form action="login_action.php" method="POST">
            <div class="title">Login</div>
            <div class="form">
                <div class="input_field">
                    <label>Email</label>
                    <input type="text" class="input" name="email" placeholder="Email" required>
                </div>
                <div class="input_field">
                    <label>Password</label>
                    <input type="password" class="input" name="password" placeholder="Password" required>
                </div>
                <div class="input_field">
                    <input type="submit" value="Login" class="btn" name="login">
                </div>
            </div>
        </form>

        <p>Don't have an account? <a href="form.php">Register here</a>.</p>
        <div class="back">
            <input type="button" value="Go Back" class="btn" onclick="window.location.href='index.php'">
        </div>
        <!-- Add a button for admin login -->
<a href="admin_login.php">
    <input type="button" value="Admin Login" class="btn">
</a>

    </div>
</body>
</html>