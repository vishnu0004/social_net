<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Post</title>
    <link rel="stylesheet" href="../css/upload.css">
    <!-- <link rel="stylesheet" href="../css/update.css"> -->
</head>

<body>
    <div class="profile-container">
        <h1>Upload New Post</h1>
        <form action="upload_post.php" method="POST" enctype="multipart/form-data">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Write something about your post..."></textarea>

            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit">Upload Post</button>
        </form>

        <a href="profile.php"> <button>Back</button></a>

    </div>
</body>

</html>
<?php
// Start session
session_start();
include('conn.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Logged-in user's ID
    $description = $_POST['description'] ?? '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $uploadDir = '../uploads/'; // Upload directory
        $imagePath = $uploadDir . basename($imageName);

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Save the post to the database
            $postQuery = "INSERT INTO posts (user_id, content, image) VALUES ('$userId', '$description', '$imagePath')";
            $data = mysqli_query($conn,$postQuery);

            if ($data) {
                echo "Post uploaded successfully!";
                header("Location: profile.php"); // Redirect to profile page
                exit;
            } else {
                echo "Error uploading post: " . $conn->error;
            }
        } else {
            echo "Failed to upload the image.";
        }
    } else {
        echo "No image was uploaded.";
    }
}
?>
