<?php
session_start();
include 'connection.php';


if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}


$user_email = $_SESSION['user_email'];

$query = "SELECT user_id, fname, email, mobile, dob, gender, interests, profile_picture FROM user WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture'])) {
        if ($_FILES['profile_picture']['error'] != 0) {
            $error_message = "Error uploading file. Error code: " . $_FILES['profile_picture']['error'];
        } else {
        
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
                $upload_dir = 'uploads/profile_pictures/';
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);  // Create directory if it doesn't exist
                }

                
                $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
                $upload_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
                    $update_query = "UPDATE user SET profile_picture = '$file_name' WHERE email = '$user_email'";
                    if (mysqli_query($conn, $update_query)) {
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
    } elseif (isset($_POST['remove_picture'])) {
        $update_query = "UPDATE user SET profile_picture = NULL WHERE email = '$user_email'";
        if (mysqli_query($conn, $update_query)) {
            if ($user['profile_picture']) {
                $file_path = 'uploads/profile_pictures/' . $user['profile_picture'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            header("Location: profile.php");
            exit;
        } else {
            $error_message = "Error removing profile picture.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Profile</title>
    <style>
        
        .profile-picture {
            padding-top: 50px;
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
            display: none; 
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
        .navbar {
            
            width: 100%;
            background-color: rgba(178, 235, 221, 0.9); 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            z-index: 1000; 
            box-sizing: border-box;
    
        }

        .navbar .exchidea {
            font-size: 24px;
            font-weight: bold;
            color: rgb(230, 160, 192);
            margin-right: 20px; 
        }

        .navbar .nav-links {
            display: flex;
            gap: 7px;
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
            background-color: rgb(130, 242, 227);
        }

    
    </style>
</head>
<body>


<div class="container">
    <div class="navbar">
      <span class="exchidea">EXCHIDEA</span>
      <div class="nav-links">
        <a href="edit_profile.php">Edit Profile</a>
        <a href="home.php">Home</a>
        <a href="logout.php">Logout</a>
      </div>
    </div>
    <h2></h2>

    <div class="profile-picture">
        <?php if ($user['profile_picture']): ?>
            <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <form action="profile.php" method="POST" class="profile-form">
                
                <button type="submit" name="remove_picture" class="btn">Remove Picture</button>
            </form>
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

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data" class="profile-form">
        <label for="profile_picture" class="file-label">Select Profile Picture</label><br>
        <input type="file" name="profile_picture" id="profile_picture" onchange="previewImage(event)" required><br>
        <div id="image_preview"></div>
        <button type="submit" class="btn">Upload Picture</button>
    </form>

    
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var imagePreview = document.getElementById('image_preview');
            imagePreview.innerHTML = '<img src="' + reader.result + '" alt="Profile Picture" class="preview-img">';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>
