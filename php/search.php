<?php
session_start();
include('conn.php');
include('header.php');

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['user_id'] ?? null;
// $profileId = $_POST['profile_id']; // Profile to follow/unfollow




// Get the search query
$query = $_GET['query'] ?? '';

if (!empty($query)) {
    // Search for the user in the database
    $searchQuery = "SELECT * FROM user WHERE username LIKE ? OR first_name LIKE ? OR last_name LIKE ?";
    $stmt = $conn->prepare($searchQuery);
    $searchTerm = "%$query%";
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();

        // Fetch the user's posts
        $postQuery = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($postQuery);
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $postsResult = $stmt->get_result();

        // Check if the logged-in user is already following this user
        $followCheckQuery = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
        $stmt = $conn->prepare($followCheckQuery);
        $stmt->bind_param('ii', $loggedInUserId, $user['id']);
        $stmt->execute();
        $isFollowing = $stmt->get_result()->num_rows > 0;
    } else {
        $error = "No user found with the given name.";
    }
} else {
    $error = "Please enter a valid search query.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>
    <section class="profile-container">
        <div class="main-d">
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php else: ?>
            <div class="profile-header">
                <!-- Display the searched user's profile -->
                <?php if (!empty($user['profile_pic'])): ?>
                    <img src="../img<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="profile-pic">
                <?php else: ?>
                    <img src="../img/boy.png" alt="Profile Picture" class="profile-pic">
                <?php endif; ?>

                <h1 style="color:#007BFF;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p class="bio"><?php echo htmlspecialchars($user['bio'] ?? 'No bio provided'); ?></p>
                <!-- <div class="stats">
                    <span><strong><?php echo $postCount ?? 0; ?></strong> Posts</span>
                    <span><strong><?php echo  $_SESSION['followers_count'] ?? 0; ?></strong> Followers</span>
                    <span><strong><?php echo  $_SESSION['following_count']  ?? 0; ?></strong> Following</span>
                </div> -->
                <!-- <form action="follow_un.php" method="GET">
                    <button type="submit" class="edit-profile-btn">Follow</button>
                </form> -->
                <!-- <form action="follow_un.php" method="POST" class="follow-form">
                    <input type="hidden" name="profile_id" value="<?php echo $user['id']; ?>">
                    <?php if ($checkFollowResult->num_rows > 0): ?>
                        <button type="submit" name="action" value="unfollow" class="unfollow-btn">Unfollow</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="follow" class="follow-btn">Follow</button>
                    <?php endif; ?>
                </form> -->

                <!-- Follow/Unfollow Button -->
                <?php if ($loggedInUserId !== $user['id']): // Prevent following yourself 
                ?>
                    <!-- <form action="follow_un.php" method="POST">
                        <input type="hidden" name="profile_id" value="<?php echo $user['id']; ?>">
                        <?php if ($isFollowing): ?>
                            <button type="submit" name="action" value="unfollow" class="follow-btn">Unfollow</button>
                        <?php else: ?>
                            <button type="submit" name="action" value="follow" class="follow-btn">Follow</button>
                        <?php endif; ?>
                    </form> -->
                <?php endif; ?>
            </div>

            <!-- Display the searched user's posts -->
            <section class="posts-section">
                <h2><?php echo htmlspecialchars($user['username']); ?>'s Posts</h2>
                <div class="posts-grid">
                    <?php while ($post = $postsResult->fetch_assoc()) : ?>
                        <div class="post">
                            <!-- Redirect to the show page with the post ID -->
                            <a href="show.php?post_id=<?php echo $post['id']; ?>">
                                <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post">
                                <p><?php echo htmlspecialchars($post['description']); ?></p>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
        </div>
    </section>
</body>

</html>