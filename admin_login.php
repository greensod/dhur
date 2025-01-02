<?php
session_start();
include 'connection.php';

// If already logged in, redirect to the admin home page
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] == true) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            color:rgb(161, 96, 97);
            text-transform: uppercase;
        }
        .container .form {
            width: 100%;
        }
        .container .form .input_field {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .container .form .input_field label {
            width: 200px;
            margin-right: 10px;
            font-size: 15px;
        }
        .container .form .input_field .input,
        .container .form .input_field .textarea {
            width: 100%;
            outline: none;
            border: 1px solid rgb(161, 96, 97);
            font-size: 15px; 
            padding: 8px 10px;
            border-radius: 3px;
            transition: all 0.5s ease;
        }
        .container .form .input_field .textarea {
            resize: none;
            height: 70px;
        }
        .container .form .input_field .custom_select {
            position: relative;
            width: 100%;
            height: 37px;
        }
        .container .form .input_field .custom_select select {
            appearance: none;
            width: 100%;
            height: 100%;
            padding: 8px 10px;
            border: 1px solid rgb(161, 96, 97);
            border-radius: 3px;
            outline: none;
        }
        .container .form .input_field .custom_select:before {
            content: "";
            position: absolute;
            top: 12px;
            right: 10px;
            border: 8px solid black;
            border-color: rgb(232, 186, 200) transparent transparent transparent;
            pointer-events: none;
        }
        .container .form .input_field .input:focus,
        .container .form .input_field .textarea:focus,
        .container .form .input_field select:focus {
            border: 2px solid rgba(249, 234, 240, 0.9);
        }
        .container .form .input_field p {
            font-size: 14px;
            color: rgb(85, 82, 82);
        }
        .container .form .input_field .check {
            width: 15px;
            height: 15px;
            position: relative;
            display: block;
            cursor: pointer;
        }
        .container .form .input_field .check input[type="checkbox"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }
        .container .form .input_field .check .checkmark {
            width: 15px;
            height: 15px;
            border: 1px solid rgb(161, 96, 97);
            border-radius: 3px;
            display: block;
            position: relative;
            top: 0;
            left: 0;
        }
        .container .form .input_field .check .checkmark:before {
            content: "";
            position: absolute;
            top: 1px;
            left: 2px;
            width: 5px;
            height: 2px;
            border: 2px solid;
            border-color: transparent transparent white white;
            transform: rotate(-45deg);
        }
        .container .form .input_field .check input[type="checkbox"]:checked ~ .checkmark {
            background: rgb(161, 96, 97);
        }
        .container .form .input_field .check input[type="checkbox"]:checked ~ .checkmark:before {
            display: block;
        }
        .container .form .input_field .btn {
            width: 100%;
            padding: 8px 10px;
            border: none;
            background:rgb(161, 96, 97);
            font-size: 15px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
            margin-bottom: 15px;
        }
        .container .form .inputfield .btn {
            width: 100%;
            padding: 8px 10px;
            border: none;
            background:rgb(161, 96, 97);
            font-size: 15px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
            margin-bottom: 6px;
        }
        .container .form .input_field:last-child {
            margin-bottom: 0;
        }
        .container .form .input_field .btn:hover {
            background: rgb(204, 139, 139);
        }
        .container .form .inputfield .btn:hover {
            background:rgb(204, 139, 139);
        }
        .container .backf .btn:hover {
            background: rgb(204, 139, 139);
        }
        .container .back .btn:hover {
            background: rgb(204, 139, 139);
        }
        .container .back .btn {
            background-color: rgb(230, 158, 195);
            border: none;
            margin: 2px;
            margin-top: 15px;
            padding: 8px 10px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
        }
        .container .backf .btn {
            background-color: rgb(230, 158, 195);
            border: none;
            padding: 8px 10px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
        }
        .container .input_field .btn {
            width: 100%;
            padding: 8px 10px;
            border: none;
            background:rgb(161, 96, 97);
            font-size: 15px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            outline: none;
            transition: all 0.5s ease;
            margin-bottom: 6px;
        }

        @media (max-width: 420px) {
            .container .form .input_field {
                flex-direction: column;
                align-items: flex-start;
            }
            .container .form .input_field label {
                margin-bottom: 5px;
            }
            .container .form .input_field.terms {
                flex-direction: row;
            }
            .container {
                padding: 20px;
            }
            .container .title {
                font-size: 20px;
            }
            .container .form .input_field .input,
            .container .form .input_field .textarea {
                width: 100%;
            }
            .container .form .input_field .custom_select {
                width: 100%;
            }
            .container .form .input_field .custom_select select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Admin Login</h1>

        <?php if (isset($error_message)) { ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php } ?>

        <form class="form" action="admin_login_action.php" method="POST">
            <div class="input_field">
                <label for="email">Email:</label>
                <input type="email" name="email" class="input" required>
            </div>
            <div class="input_field">
                <label for="password">Password:</label>
                <input type="password" name="password" class="input" required>
            </div>
            <div class="input_field">
                <button type="submit" class="btn">Login</button>        
            </div>
        </form>
        <div class="input_field">
            <a href="login.php">
               <button type="button" class="btn">Go Back</button>
            </a>        
        </div>

    </div>
</body>
</html>