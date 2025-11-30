<?php
require_once 'sql.php';
require_once 'functions.php';
session_start();

if (isset($_POST['submit'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    //update the user's password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt->bind_param("ss", $hashed_password, $token);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        header('Location: msg.php?msg=Password has been updated successfully&type=success&goto=login.php');
        exit();
    } else {
        header('Location: msg.php?msg=Failed to update password&type=error&goto=reset_password.php?token=' . urlencode($token));
        exit();
    }
}


if (isset($_GET['token'])){
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token IS NOT NULL");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        header('Location: msg.php?msg=Invalid or expired token&type=error&goto=forget_password.php');
        exit();
    }
    $user = $result->fetch_assoc();
    
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="statics/styles.css">
        <title>Reset Password</title>
    </head>
    <body>
        <h2>Reset Password for: <?php echo htmlspecialchars($user['username']); ?></h2>
        <form method="POST" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label for="new_password">New Password:</label><br>
            <input type="password" id="new_password" name="new_password" required><br><br>
            <input type="submit" name="submit" class="button" value="Update Password">
        </form>
    </body>
    </html>
    <?php
    print("Token is valid for user: " . htmlspecialchars($user['username']));"<br>";
    exit();
} ?>