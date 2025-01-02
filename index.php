<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .navbar {
            width: 100%;
            background-color: rgba(249, 234, 240, 0.9);
            padding: 5px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar .exchidea {
            font-size: 20px;
            font-weight: bold;
            color:rgb(161, 96, 97);
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            background-color: rgb(234, 201, 218);
            color: rgb(161, 96, 97);
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 30px;
            margin-left: 0px;
        }

        .btn:hover {
            background-color: rgb(156, 167, 177);
        }

        .container {
            display: flex;
            justify-content: center;
            align-items:flex-start;
            flex: 1;
            text-align: flex-start;
            padding: 20px;
        }

        .content-box {
            background-color: rgba(175, 151, 151, 0.6); /* Semi-transparent background */
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(154, 140, 140, 0.2);
            max-width: 600px; /* Optional, for readability */
            margin: auto;
            margin-top: 140px;
            color:rgb(161, 96, 97);
        }

        .terms {
            text-align: center;
            padding: 10px;
            background-color: rgba(175, 151, 151, 0.6);
            color: rgb(161, 96, 97);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .terms a {
            color:rgb(161, 96, 97);
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="exchidea">EXCHIDEA</span>
        <div class="buttons">
            <a href="login.php" class="btn">Login</a>
            <a href="form.php" class="btn">Register</a>
        </div>
    </div>

    <div class="container">
        <div class="content-box">
            <h1>EXCHIDEA-the ultimate platform for skill exchange and growth!</h1>
            <p>At EXCHIDEA, we connect individuals passionate about learning and sharing skills. Whether you're an expert in coding, a budding photographer, or someone eager to master cooking, EXCHIDEA brings people together to trade knowledge, collaborate, and grow</p>
        </div>
    </div>

    <div class="terms">
        <p><a href="terms.php">Terms and Conditions</a></p>
    </div>
</body>
</html>
