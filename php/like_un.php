<?php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'];
$action = $_POST['action'];


if ($action === 'like') {
    // Add a like
    $likeQuery = "INSERT INTO likes (post_id, user_id) VALUES ('$postId', '$userId')";
    $like = mysqli_query($conn, $likeQuery);

} elseif ($action === 'unlike') {
    // Remove a like
    $unlikeQuery = "DELETE FROM likes WHERE post_id = '$postId' AND user_id = '$userId'";
    $unlike = mysqli_query($conn, $unlikeQuery);
}

// Redirect back to the profile page
header("Location: show.php?post_id=$postId");
exit;
?>


<?php
// Check if the logged-in user is already following the searched user
// $followCheckQuery = "SELECT * FROM follows WHERE follower_id = ? AND following_id = ?";
// $stmt = $conn->prepare($followCheckQuery);
// $stmt->bind_param('ii', $userId, $user['id']);
// $stmt->execute();
// $isFollowing = $stmt->get_result()->num_rows > 0;
// $stmt->close();
?>

<!-- Follow/Unfollow Form -->
<!-- <form action="follow_unfollow.php" method="POST" style="margin: 10px;">
    <input type="hidden" name="following_id" value="<?php echo $user['id']; ?>">
    <?php if ($isFollowing): ?>
        <button type="submit" name="action" value="unfollow" class="follow-btn unfollow-btn">Unfollow</button>
    <?php else: ?>
        <button type="submit" name="action" value="follow" class="follow-btn">Follow</button>
    <?php endif; ?>
</form> -->

<?php
// $followerId = $_SESSION['user_id'];
// $followingId = $_POST['following_id'];
// $action = $_POST['action'];

// if ($action === 'follow') {
// Follow the user
// $followQuery = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
// $stmt = $conn->prepare($followQuery);
// $stmt->bind_param('ii', $followerId, $followingId);
// $stmt->execute();
// } elseif ($action === 'unfollow') {
// Unfollow the user
// $unfollowQuery = "DELETE FROM follows WHERE follower_id = ? AND following_id = ?";
// $stmt = $conn->prepare($unfollowQuery);
// $stmt->bind_param('ii', $followerId, $followingId);
// $stmt->execute();
// }

// Redirect back to the profile page or refresh the search page
// header("Location: profile.php?id=$followingId");
// exit;
?>








<!-- Comment Form -->
<!-- <form action="add_comment.php" method="POST">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
    <textarea name="comment" placeholder="Write a comment..." required></textarea>
    <button type="submit" class="comment-btn">Comment</button>
</form> -->

<!-- Display Comments -->
<!-- <div class="comment-list">
