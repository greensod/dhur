<?php
session_start();
include 'connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's email
$user_email = $_SESSION['user_email'];

// Fetch the user details
$query = "SELECT user_id, fname, email, mobile, dob, gender, interests, profile_picture FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle Profile Picture Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    // Check for any errors in the file upload
    if ($_FILES['profile_picture']['error'] != 0) {
        $error_message = "Error uploading file. Error code: " . $_FILES['profile_picture']['error'];
    } else {
        // Check the file type (you can add more types as needed)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            // Define the upload directory
            $upload_dir = 'uploads/profile_pictures/';
            
            // Ensure the directory exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);  // Create directory if it doesn't exist
            }

            // Set a unique filename for the uploaded image
            $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
            $upload_file = $upload_dir . $file_name;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
                // Update the profile picture in the database
                $update_query = "UPDATE user SET profile_picture = '$file_name' WHERE email = '$user_email'";
                if (mysqli_query($conn, $update_query)) {
                    // Redirect to the profile page after successful upload
                    header("Location: profile.php");
                    exit;
                } else {
                    $error_message = "Error updating profile picture in database.";
                }
            } else {
                $error_message = "Failed to upload file.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, or GIF are allowed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Profile</title>
    <style>
        .profile-picture {
            text-align: center;
            margin: 20px;
        }
        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-form {
            text-align: center;
            margin: 20px;
        }
        .profile-form input[type="file"] {
            display: none; /* Hide the default file input */
        }
        .file-label {
            padding: 10px 20px;
            background-color:rgb(230, 182, 206);
            color: white;
            border: none;
            cursor: pointer;
        }
        .file-label:hover {
            background-color:rgb(128, 222, 217);
        }
        .file-info {
            margin-top: 10px;
        }
        .profile-form .btn {
            padding: 10px 20px;
            background-color:rgb(230, 182, 206);
            color: white;
            border: none;
            cursor: pointer;
        }
        .profile-form .btn:hover {
            background-color:rgb(130, 242, 227);
        }
        .user-details {
            margin: 20px;
        }
        .user-details p {
            font-size: 16px;
            line-height: 1.8;
        }
        #image_preview {
            text-align: center;
            margin-top: 10px;
        }
        #image_preview img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($user['fname']); ?>!</h1>

    <div class="profile-picture">
        <?php if ($user['profile_picture']): ?>
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
        <?php else: ?>
            <p>No profile picture uploaded yet.</p>
        <?php endif; ?>
    </div>

    <div class="user-details">
        <h2>Your Profile Information:</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
        <p><strong>Interests:</strong> <?php echo htmlspecialchars($user['interests']); ?></p>
    </div>

    <h2>Edit Your Profile</h2>
    
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
        <label for="profile_picture" class="file-label">Select Profile Picture</label><br>
        <input type="file" name="profile_picture" id="profile_picture" onchange="previewImage(event)" required><br>
        <div id="image_preview"></div>
        <button type="submit" class="btn">Upload Picture</button>
    </form>

    <div class="navigation">
        <a href="edit_profile.php" class="btn">Edit Profile</a>
        <a href="home.php" class="btn">Home</a>
        <a href="logout.php" class="btn">Logout</a>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.createElement('img');
            output.src = reader.result;
            document.getElementById('image_preview').innerHTML = '';
            document.getElementById('image_preview').appendChild(output);
        };
        reader.readAsDataURL(event.target.files[0]);

        // Show file name after file is selected
        var fileName = event.target.files[0].name;
        var fileInfo = document.createElement('p');
        fileInfo.classList.add('file-info');
        fileInfo.textContent = "Selected file: " + fileName;
        document.getElementById('image_preview').appendChild(fileInfo);
    }
</script>

</body>
</html>
