<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BuddyNetwork</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1A1A1B;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .about-section {
            padding: 40px;
            background-color: #2C2F33;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>  <!-- Include navigation bar -->

<div class="container">
    <div class="about-section">
        <h2>About BuddyNetwork</h2>
        <p>Welcome to BuddyNetwork! Our social media platform is designed to connect people, share moments, and build communities. Whether you're here to chat with friends, share posts, or explore new content, BuddyNetwork provides a safe and interactive space for everyone.</p>
        
        <h3>Our Mission</h3>
        <p>We aim to bring people closer together by providing a seamless and engaging platform where users can express themselves freely and connect with like-minded individuals.</p>

        <h3>Our Team</h3>
        <ul>
            <li><strong>Gaud Badal</strong> - Founder & Backend Developer</li>
            <li><strong>John Doe</strong> - UI/UX Designer</li>
            <li><strong>Jane Smith</strong> - Frontend Developer</li>
            <li><strong>Mike Johnson</strong> - Database Administrator</li>
        </ul>

        <h3>Contact Us</h3>
        <p>Email: support@buddynetwork.com</p>
        <p>Phone: +91 9876543210</p>
    </div>
</div>


</body>
</html>
