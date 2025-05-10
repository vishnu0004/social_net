<?php
session_start();
include('conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'];
$comment = $_POST['comment'];

if (!empty($comment)) {
    $commentQuery = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($commentQuery);
    $stmt->bind_param('iis', $postId, $userId, $comment);
    $stmt->execute();
}

// Redirect back to the profile page
header("Location: show.php?post_id=$postId");
exit;
?>
