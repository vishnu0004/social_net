<?php
// Start the session
session_start();
include('conn.php');
include('header.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the post ID from the URL
if (!isset($_GET['post_id'])) {
    echo "Post not found.";
    exit;
}
$postId = $_GET['post_id'];

// Fetch the post details
$postQuery = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($postQuery);
$stmt->bind_param('i', $postId);
$stmt->execute();
$postResult = $stmt->get_result();

if ($postResult->num_rows === 0) {
    echo "Post not found.";
    exit;
}

$post = $postResult->fetch_assoc();

// Check if the logged-in user is the owner of the post
$isOwner = $post['user_id'] == $_SESSION['user_id'];

// var_dump($_SESSION['user_id'], $post['user_id']);

    // echo "<script>console.log($isOwner);</script>";



// Fetch comments for the post
$commentsQuery = "SELECT c.*, u.username FROM comments c INNER JOIN user u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at DESC";
$stmt = $conn->prepare($commentsQuery);
$stmt->bind_param('i', $postId);
$stmt->execute();
$commentsResult = $stmt->get_result();

// Get the like count for the post
$likeCountQuery = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?";
$stmt = $conn->prepare($likeCountQuery);
$stmt->bind_param('i', $postId);
$stmt->execute();
$likeCountResult = $stmt->get_result()->fetch_assoc();
$likeCount = $likeCountResult['like_count'];

// Check if the user has already liked the post
$likeCheckQuery = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
$stmt = $conn->prepare($likeCheckQuery);
$stmt->bind_param('ii', $post['id'], $_SESSION['user_id']);
$stmt->execute();
$liked = $stmt->get_result()->num_rows > 0;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link rel="stylesheet" href="../css/show.css">
</head>

<body>
<div class="head">
<div class="post-details-container">
    <!-- Left Side: Post Image and Info -->
    <div class="left-section">
        <div class="post-image-container">
            <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="Post" class="post-image">
        </div>
        <div class="post-info">
            <span class="likes"><strong><?php echo $likeCount; ?></strong> Likes</span>
            <p class="description"><?php echo htmlspecialchars($post['content']); ?></p>
            
            <!-- Like/Unlike Form -->
            <form action="like_un.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <?php if ($liked): ?>
                    <button type="submit" name="action" value="unlike" class="like-btn">Unlike</button>
                <?php else: ?>
                    <button type="submit" name="action" value="like" class="like-btn">Like</button>
                <?php endif; ?>
            </form>
            <!-- Delete Button (only for the post owner) -->
            <!-- <?php if ($isOwner): ?>
                <form action="delete_post.php" method="POST" class="delete-form">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">Delete</button>
                </form>
            <?php endif; ?> -->
        </div>
    </div>

    <!-- Right Side: Comments Section -->
    <div class="right-section">
        <div class="comments-box">
            <form action="comment.php" method="POST" class="comment-form">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <textarea name="comment" placeholder="Write a comment..." required></textarea>
                <button type="submit" class="comment-btn">Comment</button>
            </form>
        </div>
        <div class="comment-list">
            <h3>Comments</h3>
            <?php while ($comment = $commentsResult->fetch_assoc()): ?>
                <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
            <?php endwhile; ?>
        </div>
    </div>
    </div>
</div>

</body>

</html>
