<?php
require_once 'sql.php';

session_start();

if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $msg = "The username does not exist";
        $color = 'red';
    } else {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login_time'] = time();

            if(isset($_POST['remember_me'])) {
                $_SESSION['max_time'] = 3600*24*30;
            } else {
                $_SESSION['max_time'] = 3600; // 1 hour for non-remember
            }

            session_set_cookie_params(0); // always session cookie
            session_regenerate_id(true);

            $login_status = 1;
        } else {
            $msg = "The credentials are incorrect";
            $color = 'red';
            $login_status = 0;
            
        }
    }

    //log the login attempt
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $stmt = $conn->prepare("INSERT INTO login_logs (ip_address, user_agent, referrer, username, login_status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssi", $ip_address, $user_agent, $referrer, $username, $login_status);
    $stmt->execute();

    if ($login_status === 1) {
        header('Location: msg.php?msg=Login successful&type=success&goto=panel.php');
        exit();
    }
 
} 
$msg = $msg ?? '';
$color = $color ?? '#9ca3af';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title>Login</title>
</head>
<body>
    
    <form action="login.php" method="POST">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="checkbox" id="remember_me" name="remember_me">
        <label for="remember_me">Remember Me</label>

        <a href="register.php">Create an account</a>
        <a href="forget_password.php">Forgot Password?</a><br>
        <?php if($msg != ''): ?>
        <p style="color: <?php echo $color;?>"><?php echo $msg;?></p>
        <?php endif; ?>

        <input type="submit" name="submit" value="Login" class="btn login-btn">
        
    </form>
    
</body>
</html>
