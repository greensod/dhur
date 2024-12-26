<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/background.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            height: 100vh; 
            display: flex;
            flex-direction: column; 
            justify-content: space-between; 
        }

        
        .container {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; 
            padding: 20px;
            color: rgba(213, 69, 132, 0.6);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6); 
        }

        
        .buttons {
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: rgb(230, 182, 206);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background-color: rgb(130, 242, 227);
        }

        
        .terms {
            text-align: center;
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.6); 
            color: white;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .terms a {
            color: white;
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Welcome to EXCHIDEA!</h1>
        <p> We offer great services and products!</p>
        
        <div class="buttons">
            <a href="login.php" class="btn">Login</a>
            <a href="form.php" class="btn">Register</a>
        </div>
    </div>

    <div class="terms">
        <p><a href="terms.php">Terms and Conditions</a></p>
    </div>

</body>
</html>
