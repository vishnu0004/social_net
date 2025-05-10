<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="signup-container">
        <h1>Login</h1>

        <?php
         session_start();
            include('conn.php');
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Check if the username exists
            $query = "SELECT * FROM user WHERE username = '$username'";
            $data = mysqli_query($conn,$query);
            if ($data->num_rows > 0) {
                // Fetch user data
                $user = $data->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Start session and store user data
                   
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    echo "<p style='color: green;'>Login successful! Redirecting...</p>";
                    header("Refresh: 2; url=feed.php");
                } else {
                    echo "<p style='color: red;'>Incorrect password. Please try again.</p>";
                }
            } else {
                echo "<p style='color: red;'>Username not found. Please sign up first.</p>";
            }
        }
        ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </div>
</body>

</html>
