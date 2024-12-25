<?php include 'connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Form</title>
</head>
<body>
    <div class="container">
        <form action="#" method="POST">
        <div class="title">
            Registration Form
        </div>
        <div class="form">
            <div class="input_field">
                <label>Full Name</label>
                <input type="text" class="input" name="fname" placeholder="Full Name" required>
            </div>
            <div class="input_field">
                <label>Email Address</label>
                <input type="text" class="input" name="email" placeholder="Email" required>
            </div>
            <div class="input_field">
                <label>Password</label>
                <input type="password" class="input" name="password" placeholder="Password" required>
            </div>
            <div class="input_field">
                <label>Mobile Number</label>
                <input type="text" class="input" name="mobile" placeholder="Mobile Number" required>
            </div>
            <div class="input_field">
                <label>Date of Birth</label>
                <input type="date" class="input" name="dob" placeholder="Date of Birth" required>
            </div>
            <div class="input_field">
                <label>Gender</label>
                <div class="custom_select">
                <select name="gender" required>
                    <option value="Not Selected">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            </div>
            <div class="input_field">
                <label for="interests">Interests</label>
                <input type="text" class="input" name="interests" id="interests" placeholder="Interests" required>
                <!-- <label>Interests</label>
                <textarea class="textarea" name="interests" placeholder="Interests" required></textarea> -->

            </div>
            <div class="input_field terms">
                <label class="check">
                    <input type="checkbox" name="terms" required>
                    <span class="checkmark"></span>
                </label>
                <p>Agree with terms and conditions</p>
                
            </div>
            <div class="input_field">
                <input type="submit" value="Register" class="btn" name="register">
            </div>
            <div class="input_field">
                <input type="button" value="Login" class="btn" onclick="window.location.href='login.php'">
            </div>

        </div>
        </form>
    </div>
</body>
</html>

<?php
    /*if(isset($_POST['register'])){
        $fname = $_POST['fname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $mobile = $_POST['mobile'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $interests = $_POST['interests'];

        $query = "INSERT INTO user VALUES ('$fname', '$email', '$password', '$mobile', '$dob', '$gender', '$interests')";
        $data=mysqli_query($conn, $query);

        if ($data){
            echo "Data inserted into database";
        }
        else{
            echo "Failed to insert data into database";
        }

    }


?>*/
 /*error_reporting(E_ALL);
 ini_set('display_errors', 1);
 if (isset($_POST['register'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);

    // Validate inputs
    if (empty($fname) || empty($email) || empty($password) || empty($mobile) || empty($dob) || $gender === "Not Selected") {
        echo "Please fill in all fields.";
        exit;
    }

    // Validate checkbox
    if (!isset($_POST['terms'])) {
        echo "You must agree to the terms and conditions.";
        exit;
    }

    // Insert data
    // $query = "INSERT INTO user (fname, email, password, mobile, dob, gender, interests) 
    //           VALUES ('$fname', '$email', '$password', '$mobile', '$dob', '$gender', '$interests')";
    
    // $data = mysqli_query($conn, $query);
    $check_query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "You are already registered! Redirecting to <a href='login.php'>login page</a>.";
        exit;
    }
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
     
    $stmt = $conn->prepare("INSERT INTO user (fname, email, password, mobile, dob, gender, interests) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fname, $email, $hashed_password, $mobile, $dob, $gender, $interests);
    // if ($data) {
    //     echo "Data inserted into database";
    // } else {
    //     echo "Failed to insert data into database: " . mysqli_error($conn);
    // }
    
    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to <a href='login.php'>login page</a>.";
        exit;
    } else {
        echo "Failed to insert data into database: " . $stmt->error;
    }
    $stmt->close();


}
?>*/
    /*error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if (isset($_POST['register'])) {
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $interests = mysqli_real_escape_string($conn, $_POST['interests']);

        // Validate inputs
        if (empty($fname) || empty($email) || empty($password) || empty($mobile) || empty($dob) || $gender === "Not Selected") {
            echo "Please fill in all fields.";
            exit;
        }

        // Validate checkbox
        if (!isset($_POST['terms'])) {
            echo "You must agree to the terms and conditions.";
            exit;
        }

        // Check if the email already exists
        $check_query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($result) > 0) {
            echo "You are already registered! Redirecting to <a href='login.php'>login page</a>.";
            exit;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Use a prepared statement to insert data
        $stmt = $conn->prepare("INSERT INTO user (fname, email, password, mobile, dob, gender, interests) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fname, $email, $hashed_password, $mobile, $dob, $gender, $interests);
        
        if ($stmt->execute()) {
            echo "Registration successful! Redirecting to <a href='login.php'>login page</a>.";
            exit;
        } else {
            echo "Failed to insert data into database: " . $stmt->error;
        }
        
        $stmt->close();
    }
?>*/

 include 'connection.php';
 session_start();
 error_reporting(E_ALL);
 ini_set('display_errors', 1);

 if (isset($_POST['register'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);

    // Validate inputs
    if (empty($fname) || empty($email) || empty($password) || empty($mobile) || empty($dob) || $gender === "Not Selected") {
        echo "Please fill in all fields.";
        exit;
    }

    // Validate checkbox
    if (!isset($_POST['terms'])) {
        echo "You must agree to the terms and conditions.";
        exit;
    }

    // Check if the email already exists
    $check_query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "You are already registered! Redirecting to <a href='login.php'>login page</a>.";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Use a prepared statement to insert data
    $stmt = $conn->prepare("INSERT INTO user (fname, email, password, mobile, dob, gender, interests) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fname, $email, $hashed_password, $mobile, $dob, $gender, $interests);

    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to login page...";
        header("Refresh: 2; url=login.php"); // Redirect after 2 seconds
        exit;
    } else {
        echo "Failed to insert data into database: " . $stmt->error;
    }
    $stmt->close();
}
?>