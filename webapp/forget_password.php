<?php
require_once 'sql.php';
require_once 'functions.php';
session_start();

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $random_token = random_token();
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Username not found!");
    }

    //Fatal error: Uncaught mysqli_sql_exception: Data too long for column 'reset_token' at row 1 in /var/www/html/webapp/forget_password.php:23 Stack trace: #0 /var/www/html/webapp/forget_password.php(23): mysqli_stmt->execute() #1 {main} thrown in /var/www/html/webapp/forget_password.php on line 23
    
    $user = $result->fetch_assoc();
   //update the user's reset token in the database
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE username = ?");
    $stmt->bind_param("ss", $random_token, $username);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $to = $user['email'];
        $subject = "Password Reset Request";
        $reset_link = "http://amin4fg.com/reset_password.php?token=" . $random_token;
        $message = "Click the following link to reset your password: " . $reset_link;
        $headers = "From: amin4fg <no-reply@amin4fg.com>";
        echo("Sending email to: " . $to . "<br>");
        mail($to, $subject, $message, $headers);
        
        header('Location: msg.php?msg=Password reset link has been sent to your email&type=success&goto=login.php');
        exit();
    }   
    else {
        header('Location: msg.php?msg=Failed to send reset link&type=error&goto=forget_password.php');
        exit();
    }

    
} else {

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title>Forget Password</title>
</head>
<body>
    <h2>Forget Password</h2>
    <form method="POST" action="forget_password.php">
        <label for="username">Enter your registered username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <input type="submit" name="submit" class="button" value="Reset Password">

</body>
</html>
<?php } ?>