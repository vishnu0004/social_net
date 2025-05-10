<?php
session_start();
include('conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION['user_id']; // Logged-in user's ID
$profileId = $_POST['profile_id']; // Profile to follow/unfollow
$action = $_POST['action']; // Follow or Unfollow

if ($action === 'follow') {
    // Check if already following
    $checkQuery = "SELECT * FROM follows WHERE follower_id = '$userId' AND following_id = '$profileId'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) === 0) {
        // Add follow relationship
        $followQuery = "INSERT INTO follows (follower_id, following_id) VALUES ('$userId', '$profileId')";
        if (mysqli_query($conn, $followQuery)) {

            // Get followers count
            $followersQuery = "SELECT COUNT(*) AS followers_count FROM follows WHERE following_id = '$profileId'";
            $folo = mysqli_query($conn,$followersQuery);
            if ($folo->num_rows > 0) {
                // Fetch user data
                $user = $folo->fetch_assoc();
                $_SESSION['followers_count'] = $user['followers_count'];
                // header("location : search.php");
            }
            echo $_SESSION['followers_count'];
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Already following this user.";
    }
} elseif ($action === 'unfollow') {
    // Remove follow relationship
    $unfollowQuery = "DELETE FROM follows WHERE follower_id = '$userId' AND following_id = '$profileId'";
    if (mysqli_query($conn, $unfollowQuery)) {

        // Get following count
        $followingQuery = "SELECT COUNT(*) AS following_count FROM follows WHERE follower_id = '$profileId'";
            $unfolo = mysqli_query($conn,$followingQuery);
            if ($unfolo->num_rows > 0) {
                // Fetch user data
                $user = $unfolo->fetch_assoc();
                $_SESSION['following_count'] = $user['following_count'];
                // header("location : search.php");
            }
            echo $_SESSION['following_count'];
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid action.";
}
