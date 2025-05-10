<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>

<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <p>Create your account</p>

        <?php

        include('conn.php');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        

            // Retrieve form data
            $firstName = $_POST['first_name'];
            $lastName = $_POST['last_name'];
            $username = $_POST['username'];
            $gender = $_POST['gender'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

            // Check if username or email already exists
            $checkQuery = "SELECT id FROM user WHERE username = '$username' OR email = '$email'";
           $data = mysqli_query($conn,$checkQuery);

            if ($data->num_rows > 0) {
                echo "<p style='color: red;'>Username or Email already exists. Please try again.</p>";
            } else {
                // Insert new user
                $insertQuery = "INSERT INTO user (first_name, last_name, username, gender, email, password) 
                                VALUES ('$firstName', '$lastName', '$username', '$gender', '$email', '$password')";
        
                if (mysqli_query($conn, $insertQuery)) {
                    echo "<p style='color: green;'>Signup successful! Redirecting...</p>";
                    header("Refresh: 20; url=login.php");
                } else {
                    echo "<p style='color: red;'>Error: Could not create account. Please try again later.</p>";
                }
            }
        }
        ?>

        <!-- Signup Form -->
        <form action="" method="POST">
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" name="first_name" placeholder="Enter your first name" required>

            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" name="last_name" placeholder="Enter your last name" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <div class="gender-options">
                <label>Gender</label>
                <label>
                    <input type="radio" name="gender" value="male" checked> Male
                </label>
                <label>
                    <input type="radio" name="gender" value="female"> Female
                </label>
                <label>
                    <input type="radio" name="gender" value="other"> Other
                </label>
            </div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Login Now</a></p>
    </div>
</body>

</html>
