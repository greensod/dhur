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


$skills_query = "SELECT skill_name FROM user_skills WHERE user_id = {$user['user_id']}";
$skills_result = mysqli_query($conn, $skills_query);
$skills = [];
while ($row = mysqli_fetch_assoc($skills_result)) {
    $skills[] = $row['skill_name'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture'])) {
        if ($_FILES['profile_picture']['error'] != 0) {
            $error_message = "Error uploading file. Error code: " . $_FILES['profile_picture']['error'];
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
                $upload_dir = 'uploads/profile_pictures/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = time() . '_' . basename($_FILES['profile_picture']['name']);
                $upload_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
                    $update_query = "UPDATE user SET profile_picture = '$file_name' WHERE email = '$user_email'";
                    mysqli_query($conn, $update_query);
                    header("Location: profile.php");
                    exit;
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
    <title>Profile</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9f9f9;
        color: #333;
    }

    .navbar {
    width: 100%;
    background-color: #ffe4e6;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    box-sizing: border-box;
}

.container {
    margin-top: 80px; 
}


    .navbar .exchidea {
        font-size: 24px;
        font-weight: bold;
        color: #e892a3;
        margin-right: 20px;
        text-decoration: none;
    }

    .navbar .nav-links {
        display: flex;
        gap: 10px;
    }

    .navbar .nav-links a {
        text-decoration: none;
        padding: 8px 15px;
        background-color: #f7d9dc;
        color: #933a4d;
        border-radius: 5px;
        font-weight: bold;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .navbar .nav-links a:hover {
        background-color: #e0c4c8;
    }

    .profile-picture {
        padding-top: 80px;
        text-align: center;
        margin: 20px;
    }

    .profile-picture img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 5px;
        border: 3px solid #e892a3;
    }

    .profile-form {
        text-align: center;
        margin: 20px;
    }

    .file-label {
        padding: 10px 20px;
        background-color: #ffe4e6;
        color: #933a4d;
        border: none;
        cursor: pointer;
        display: inline-block;
        margin-bottom: 0px;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .file-label:hover {
        background-color: #e0c4c8;
    }

    .btn {
        padding: 10px 20px;
        background-color: #ffe4e6;
        color: #933a4d;
        border: none;
        cursor: pointer;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #e0c4c8;
    }

    .user-details {
        margin: 40px 20px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .user-details h2 {
        margin-bottom: 20px;
        color: #933a4d;
    }

    .user-details p {
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 10px;
    }

    .user-skills {
        margin: 40px 20px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .user-skills h2 {
        margin-bottom: 20px;
        color: #933a4d;
    }

    .user-skills table {
        width: 100%;
        border-collapse: collapse;
        background-color: #f9f9f9;
        font-size: 16px;
    }

    .user-skills th, .user-skills td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    .user-skills th {
        background-color: #ffe4e6;
        color: #933a4d;
        font-weight: bold;
    }

    .user-skills td {
        background-color: #fff;
    }

    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .navbar {
            flex-wrap: wrap;
        }

        .navbar .nav-links {
            flex-direction: column;
            align-items: center;
        }

        .navbar .nav-links a {
            margin: 5px 0;
        }

        .profile-picture img {
            width: 120px;
            height: 120px;
        }
    }
</style>

    <script>
        function previewImage(event) {
            const output = document.getElementById('profilePicturePreview');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <a href="home.php" class="exchidea">EXCHIDEA</a>
            <div class="nav-links">
                <a href="edit_profile.php">Edit Profile</a>
                <a href="add_skills.php">Add Skills</a> 
                <a href="home.php">Home</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="profile-picture">
            <?php if ($user['profile_picture']): ?>
                <img src="uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                
                <form action="profile.php" method="POST" class="profile-form">
                    <input type="hidden" name="remove_picture" value="1">
                    <button type="submit" class="btn" onclick="return confirm('Are you sure you want to remove the picture?');">
                        Remove Picture
                    </button>
                </form>
            <?php else: ?>
                <img src="images/default_profile_picture.jpg" alt="Default Profile Picture">
            <?php endif; ?>

            
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <?php if (!$user['profile_picture']): ?>
                    <label for="profile_picture" class="file-label">Select Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(event)" style="display:none;">
                    <br><br>
                    <img id="profilePicturePreview" style="display:none; width:150px; height:150px; border-radius:50%; margin-top: 10px;" alt="Profile Picture Preview">
                    <br><br>
                    <button type="submit" class="btn">Upload Picture</button>
                <?php endif; ?>
            </form>
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

        <div class="user-skills">
            <h2>Your Skills:</h2>
            <?php if (!empty($skills)): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Skill Name</th>
                            <th>Level</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $skills_query = "SELECT skill_name, level, duration FROM user_skills WHERE user_id = {$user['user_id']}";
                        $skills_result = mysqli_query($conn, $skills_query);

                        while ($row = mysqli_fetch_assoc($skills_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['skill_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['level']); ?>/5</td>
                                <td><?php echo htmlspecialchars($row['duration']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No skills added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
