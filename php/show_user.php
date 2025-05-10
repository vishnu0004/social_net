<?php
session_start();
include('conn.php');
include('header.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_GET['user_id'];
// Fetch logged-in user details
// $userId = $_SESSION['post_user_id'];
$userQuery = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Fetch post count
$postCountQuery = "SELECT COUNT(*) AS post_count FROM posts WHERE user_id = ?";
$stmt = $conn->prepare($postCountQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$postCountResult = $stmt->get_result();
$postCount = $postCountResult->fetch_assoc()['post_count'];

// Fetch follower and following counts
$followerQuery = "SELECT COUNT(*) AS follower_count FROM follows WHERE following_id = ?";
$stmt = $conn->prepare($followerQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$followerResult = $stmt->get_result();
$followerCount = $followerResult->fetch_assoc()['follower_count'];

$followingQuery = "SELECT COUNT(*) AS following_count FROM follows WHERE follower_id = ?";
$stmt = $conn->prepare($followingQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$followingResult = $stmt->get_result();
$followingCount = $followingResult->fetch_assoc()['following_count'];

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>
    <section class="profile-container">
        <div class="main-d">
        <!-- Header Section -->
        <div class="profile-header">
            <img 
                src="<?php echo !empty($user['profile_pic']) ? '../update_img/' . htmlspecialchars($user['profile_pic']) : '../img/boy.png'; ?>" 
                alt="Profile Picture" 
                class="profile-pic">

            <h5><?php echo htmlspecialchars($user['username']); ?></h5><br>
            <h1 style="color: #007BFF;">
                <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?>
            </h1>
            <p class="bio">
                <?php echo htmlspecialchars($user['bio'] ?? 'No bio provided'); ?>
            </p>

            <div class="stats">
                <span><strong><?php echo $postCount ?? 0; ?></strong> Posts</span>
                <span><strong><?php echo $followerCount ?? 0; ?></strong> Followers</span>
                <span><strong><?php echo $followingCount ?? 0; ?></strong> Following</span>
            </div>
        </div>

        <!-- Posts Section -->
        <section class="posts-section">
            <h2>Your Posts</h2>
            <div class="posts-grid">
                <?php
                $postQuery = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
                $stmt = $conn->prepare($postQuery);
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $postResult = $stmt->get_result();

                if ($postResult->num_rows > 0):
                    while ($post = $postResult->fetch_assoc()): ?>
                        <div class="post">
                            <a href="show.php?post_id=<?php echo htmlspecialchars($post['id']); ?>">
                                <img 
                                    src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" 
                                    alt="Post">
                                <p><?php echo htmlspecialchars($post['content']); ?></p>
                            </a>
                        </div>
                    <?php endwhile;
                else: ?>
                    <p>No posts to display. Create your first post now!</p>
                <?php endif; ?>
            </div>
        </section>
        </div>
    </section>
</body>

</html>
