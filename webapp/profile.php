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
        header('Location: login.php');
        exit();
    }
}

$id = $_GET['user_id'];
$is_own_profile = isset($_SESSION['login']) && $_SESSION['login'] === true && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id;


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$profile_picture = $user['profile_picture'] ?: 'default.png';
$profile_picture_url = 'http://static.amin4fg.com/user_profiles/' . rawurlencode($profile_picture);

if (!$user) {
    header('Location: msg.php?msg=User not found&type=error&goto=panel.php');
    exit();
}
//call internal api and fetch user information with php
$api_url = "http://localhost:5000/api/user/" . urlencode($id);
$api_response = @file_get_contents($api_url);
if ($api_response !== false) {
    $api_data = json_decode($api_response, true);
    $user_info = $api_data;
} else {
    $user_info = null;
    echo "<p style='color: red;'>Could not fetch user info from API for user_id: " . htmlspecialchars($id) . "</p>";
}

// Fetch user tweets
$stmt_tweets = $conn->prepare("SELECT * FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
$stmt_tweets->bind_param("i", $id);
$stmt_tweets->execute();
$result_tweets = $stmt_tweets->get_result();
$tweets = [];
while ($row = $result_tweets->fetch_assoc()) {
    $tweets[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title><?php echo htmlspecialchars($user_info['username'] ?? $user['username']); ?>'s Profile</title>
</head>
<body>
    <div class="panel">
        <div class="panel-header">
            <h1><?php echo htmlspecialchars($user_info['username'] ?? $user['username']); ?>'s Profile</h1>
            <div>
                <?php if($is_own_profile): ?>
                    <a href="index.php">Home</a> | <a href="panel.php">Back to Panel</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="profile">
            <img src="<?php echo htmlspecialchars($profile_picture_url); ?>" alt="Profile Picture" class="profile-img">
            <div class="profile-meta">
                <div class="profile-name"><?php echo htmlspecialchars($user_info['name'] ?? $user['name']); ?></div>
                <span class="muted">(<?php echo htmlspecialchars($user_info['username'] ?? $user['username']); ?>)</span>
                <?php if (!empty($user_info['bio'] ?? $user['bio'])): ?>
                    <div class="profile-bio"><?php echo htmlspecialchars($user_info['bio'] ?? $user['bio']); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tweets">
            <div class="tweets-header">
                <h2 style="text-align: center;">Tweets</h2>
                <p style="text-align: center; color: #666;">total tweets: <?php echo count($tweets); ?></p>
            </div>
            <?php if (empty($tweets)): ?>
                <p>No tweets yet.</p>
            <?php else: ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet" style="border: 1px solid rgba(0, 100, 150, 0.3); border-radius: 8px; padding: 15px; margin-bottom: 10px; background: rgba(255, 255, 255, 0.05); position: relative;">
                        <?php if($is_own_profile): ?>
                            <a href="delete.php?post_id=<?php echo $tweet['id']; ?>" style="position: absolute; top: 5px; right: 5px; color: red; text-decoration: none; font-size: 20px;">×</a>
                        <?php endif; ?>
                        <p style="margin: 0 0 10px 0; line-height: 1.5;"><?php echo htmlspecialchars($tweet['content']); ?></p>
                        <small style="color: #b0c4de; font-size: 0.85em;"><?php echo htmlspecialchars($tweet['created_at']); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>