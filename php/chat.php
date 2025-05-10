<?php
session_start();
include('conn.php');
include('header.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Insert a new message if provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['msg']) && !empty($_POST['msg'])) {
    $msg = mysqli_real_escape_string($conn, $_POST['msg']); // Sanitize input
    $q = "INSERT INTO `msg`(`username`, `msg`) VALUES ('$username', '$msg')";
    mysqli_query($conn, $q);

    // Redirect to the same page to prevent form resubmission
    header("Location: chat.php");
    exit; // Ensure the script stops execution after the redirect
}

// Fetch all messages from the database
$q = "SELECT * FROM `msg` ORDER BY id ASC";
$result = mysqli_query($conn, $q);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/chat.css">
    <title>Chat Room</title>
</head>

<body>
    <div class="chat">
        <h2 style="color: white;">Welcome, <span ><?php echo $username; ?></span></h2>
        <div class="msg">
            <?php
            // Display all messages
            while ($row = mysqli_fetch_assoc($result)) {
                $sender = $row['username'];
                $message = htmlspecialchars($row['msg']); // Sanitize message to prevent XSS
                $class = ($sender === $username) ? "sent" : "received"; // Add a class based on the sender
                echo "<p class='$class'><span>$sender:</span> $message</p>";
            }
            ?>
        </div>

        <form method="POST" action="chat.php" class="input-msg">
            <input type="text" name="msg" placeholder="Type your message here" id="input_msg" required>
            <button type="submit">Send</button>
        </form>
    </div>
</body>

<script>
    // Automatically scroll to the bottom of the chat window
    function scrollToBottom() {
        const chatBox = document.querySelector('.msg');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Ensure the chat stays scrolled to the bottom on page load
    window.onload = scrollToBottom;
</script>

</html>
