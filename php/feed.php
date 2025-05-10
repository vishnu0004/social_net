<?php
session_start();
include('conn.php');
include('header.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id']; // Logged-in user's ID

// Fetch random posts from the database
$randomPostsQuery = "
    SELECT posts.*, user.username, user.profile_pic, user.id AS user_id
    FROM posts
    JOIN user ON posts.user_id = user.id
    ORDER BY RAND()
    LIMIT 20";
$result = $conn->query($randomPostsQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Feed</title>
    <link rel="stylesheet" href="../css/feed.css">
</head>

<body>
    <div class="feed-container">
       <span><h1>Explore Posts</h1></span> 
      <!-- <span><a href="profile.php">profile page</a></span><br><br> -->

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($post = $result->fetch_assoc()):  $_SESSION['post_user_id'] = $post['user_id'];?>
                <div class="post">
                    <div class="post-header">
                        <!-- Link to user's profile -->
                        <a href="show_user.php?user_id=<?php echo htmlspecialchars($_SESSION['post_user_id'] ); ?>">
                            <!-- Display user profile picture -->
                            <img 
                                src="<?php echo !empty($post['profile_pic']) ? '../update_img/' . htmlspecialchars($post['profile_pic']) : '../img/boy.png'; ?>" 
                                alt="Profile Picture" 
                                class="profile-pic">
                        </a>
                        <!-- Display username -->
                        <a href="show_user.php?user_id=<?php echo htmlspecialchars($post['user_id']); ?>">
                            <h3><?php echo htmlspecialchars($post['username']); ?></h3>
                        </a>
                    </div>

                    <!-- Display post content -->
                    <div class="post-content">
                        <?php if (!empty($post['image'])): ?>
                            <center>
                                <a href="show.php?post_id=<?php echo htmlspecialchars($post['id']); ?>">
                                    <img 
                                        src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" 
                                        alt="Post Image" 
                                        class="post-image">
                                </a>
                            </center>
                        <?php endif; ?>
                    </div>

                    <!-- Display post timestamp -->
                    <div class="post-footer">
                        <span>Posted on: <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available. Be the first to post something!</p>
        <?php endif; ?>
    </div>
</body>

</html>
