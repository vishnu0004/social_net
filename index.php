<?php
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: ./php/login.php");
    exit;
}
// Include the common header
include('./php/header.php');

// Get the requested page
$page = $_GET['page'] ?? 'home';

// Load different content based on the page
if ($page === 'home') {
    include('./php/home.php');
} elseif ($page === 'profile') {
    include('./php/profile.php');
} elseif ($page === 'search') {
    include('./php/search.php');
} else {

    include('./php/404.php'); // Custom 404 page for invalid routes
}
?>