<?php
session_start();
require_once 'sql.php';

if (isset($_SESSION['login'])) {
    if (time() - $_SESSION['login_time'] > $_SESSION['max_time']) {
        session_destroy();
        session_unset();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        header('Location: msg.php?msg=Session expired&type=error&goto=login.php');
        exit();
    }
}

if (!isset($_SESSION['login'])) {
    header('Location: msg.php?msg=You are not logged in&type=error&goto=login.php');
    exit();

}
if (isset($_GET['logout'])) {
    session_destroy();
    session_unset();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    header('Location: msg.php?msg=Logged out successfully&type=success&goto=login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_picture = $user['profile_picture'] ?: 'default.png';
$profile_picture_url = 'http://static.amin4fg.com/user_profiles/' . rawurlencode($profile_picture);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title>Panel</title>
</head>
<body>
    <div class="panel">
        <div class="panel-header">
            <h1>Panel</h1>
            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                <a href="index.php">Home</a>
                <a href="profile.php?user_id=<?php echo $user_id; ?>">Public Profile</a>
                <a href="?logout=1">Logout</a>
            </div>
        </div>

        <div class="profile">
            <img src="<?php echo htmlspecialchars($profile_picture_url); ?>" alt="Profile Picture" class="profile-img">
            <div class="profile-meta">
                <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
                <span class="muted">(<?php echo htmlspecialchars($user['username']); ?>)</span>
                <?php if (!empty($user['bio'])): ?>
                    <div class="profile-bio"><?php echo htmlspecialchars($user['bio']); ?></div>
                <?php endif; ?>
            </div>
        </div>
 
       <form action="update_profile.php" method="POST" enctype="multipart/form-data">
           <label for="name_input">Name:</label>
           <input id="name_input" type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">

           <label for="username_input">Username:</label>
           <input id="username_input" type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>

           <label for="email_input">Email:</label>
           <input id="email_input" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly disabled>

           <label for="bio_input">Bio:</label>
           <textarea class="textarea" id="bio_input" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>

           <label for="new_password_input">New Password:</label>
           <input id="new_password_input" type="password" name="new_password">
           <small>Leave blank to keep current password</small><br>

           <label for="profile_picture_input">Profile Picture:</label>
           <input id="profile_picture_input" type="file" name="profile_picture" accept="image/*">
           <small class="muted">PNG, JPG or GIF up to 2MB.</small>

           <input type="hidden" name="current_profile_picture" value="<?php echo htmlspecialchars($profile_picture); ?>">
           <button type="submit" name="submit">Update</button>
       </form>
   </div>
</body>
</html>