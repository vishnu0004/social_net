<?php
// session_start();
include('conn.php');

// Check if the user is logged in
$userProfilePic = '../img/boy.png'; // Default profile picture
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT profile_pic FROM user WHERE id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!empty($_SESSION['profile_pic'])) {
        $userProfilePic = "../img" . htmlspecialchars($_SESSION['profile_pic']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css"> <!-- Link your CSS file -->
    <title>Navbar</title>
</head>

<body>
    <nav class="navba">
        <div class="navba-container">

            <!-- Logo or Home -->
            <div>
                <a href="feed.php" class="navba-logo">BuddyNetwork</a>
            </div>
            <!-- Links -->
            <ul class="navba-links">
                <li><a href="feed.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="chat.php">GroupChat</a></li>
                <li><a href="logout.php">Logout</a></li>
                <li>
                    <form action="search.php" method="GET" class="searc-form">
                        <input type="text" name="query" placeholder="Search for a user..." required>
                        <button type="submit">Search</button>
                    </form>

                </li>
            </ul>

            <!-- Profile Picture -->
            <div class="profil">
                <a href="profile.php">
                    <img src="<?php echo $userProfilePic; ?>" alt="Profile Picture" class="profil-pic">
                </a>
            </div>
        </div>
    </nav>
</body>

</html>