<?php
session_start();

include('conn.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$errorMsg = "";
$successMsg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $bio = $_POST['bio'];

    // Handle profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = uniqid() . "-" . $_FILES['profile_pic']['name'];
        $uploadDir = '../update_img/';
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Update profile picture in database
            $updatePicQuery = "UPDATE user SET profile_pic = '$fileName' WHERE id = '$userId'";
            $data = mysqli_query($conn,$updatePicQuery);
        } else {
            $errorMsg = "Error uploading profile picture.";
        }
    }

    // Update name and bio in database
    $updateUserQuery = "UPDATE user SET first_name =  '$firstName', last_name = '$lastName', bio = '$bio' WHERE id = '$userId' ";
    $data = mysqli_query($conn,$updateUserQuery);

    if ($data) {
        $successMsg = "Profile updated successfully.";
        header("location:profile.php");
    } else {
        $errorMsg = "Error updating profile.";
    }
}

// Fetch updated user details
$userQuery = "SELECT * FROM user WHERE id = '$userId'";
$data = mysqli_query($conn,$userQuery);

if ($data->num_rows > 0) {
    $user = $data->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../css/update.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>
    <section class="profile-container">
        <div class="main-d">
        <!-- Display Success/Error Messages -->
        <?php if ($successMsg): ?>
            <p style="color: green;"><?php echo $successMsg; ?></p>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <p style="color: red;"><?php echo $errorMsg; ?></p>
        <?php endif; ?>

        <!-- Profile Section -->
        <header class="profile-header">
        <?php

        // echo $user['profile_pic'];
        if($user['profile_pic']){
            ?>
                         <img src="../update_img/<?php echo $user['profile_pic']?>" alt="Profile Picture" class="profile-pic">
                          
       <?php }
            else{?>
                      <img src="../img/boy.png" alt="Profile Picture" class="profile-pic">
      <?php  }?>            <h1><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h1>
            <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio provided'); ?></p>
        </header>

        <!-- Edit Profile Form -->
        <section class="edit-profile-section">
            <h2>Edit Profile</h2>
            <form method="POST" action="update.php" enctype="multipart/form-data">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Write about yourself"><?php echo htmlspecialchars($user['bio']); ?></textarea>

                <label for="profile_pic">Profile Picture:</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

                <button type="submit">Update Profile</button>
                <a href="profile.php"> <button type="submit">Back</button></a>
            </form>
        </section>
        </div>
    </section>
</body>

</html>
