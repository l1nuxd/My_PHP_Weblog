<?php session_start();
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
        // Since it's index, perhaps redirect or just unset
        // But to keep it simple, unset session
        // But since destroyed, and no redirect, the page will show as not logged in
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <style>
        body { display: flex; justify-content: center; align-items: flex-start; padding-top: 50px; }
        .tweet {
            border: 1px solid rgba(0, 100, 150, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            cursor: default;
            position: relative;
        }
        .tweet:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
    <title>Home</title>
</head>
<body>
<div id="message" class="message success" style="display:none; position: absolute; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10;"></div>
<?php if(!isset($_SESSION['login'])): ?>
<div style="position: absolute; top: 20px; right: 20px; z-index: 10; display: flex; gap: 10px; background: rgba(0, 20, 40, 0.9); border: 1px solid rgba(0, 100, 150, 0.3); border-radius: 8px; padding: 5px 10px; backdrop-filter: blur(10px);">
    <a href="login.php" style="text-decoration: none;"><button style="padding: 8px 15px; font-size: 14px; background: none; border: none; color: #e0e0e0;">Login</button></a>
    <a href="register.php" style="text-decoration: none;"><button style="padding: 8px 15px; font-size: 14px; background: none; border: none; color: #e0e0e0;">Sign Up</button></a>
</div>
<?php endif; ?>
<div class="panel" style="position: relative;">
<?php
$user = null;
$profile_picture_url = '';
if(isset($_SESSION['login'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $profile_picture = $user['profile_picture'] ?: 'default.png';
    $profile_picture_url = 'http://static.amin4fg.com/user_profiles/' . rawurlencode($profile_picture);
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <?php if($user): ?>
        <div class="profile">
            <img src="<?php echo htmlspecialchars($profile_picture_url); ?>" alt="Profile Picture" class="profile-img">
            <div class="profile-meta">
                <div class="profile-name"><?php echo htmlspecialchars($user['name']); ?></div>
                <span class="muted">(<?php echo htmlspecialchars($user['username']); ?>)</span>
            </div>
        </div>
    <?php else: ?>
        <div></div>
    <?php endif; ?>
    <div>
        <?php if($user): ?>
        <a href="profile.php?user_id=<?php echo htmlspecialchars($user['id'] ?? ''); ?>">My Public Profile</a><br>
        <a href="panel.php">Open Panel</a><br>
        <a href="panel.php?logout=1">Logout</a>

        <?php endif; ?>
    </div>
</div>
<?php if($user): ?>
<form>
    <label for="tweet-content">What's on your mind?</label><br>
    <div style="display: flex; flex-direction: column; align-items: flex-start;">
        <textarea name="content" placeholder="Post a tweet..." rows="4" cols="56" style="width: 100%;"></textarea>
        <button type="button" id="btn" name="post_tweet" style="align-self: flex-end;">Tweet!</button>
    </div>
</form>
<?php endif; ?>
<div class="tweets">
    <div class="tweets-header">
        <h2 style="text-align: center;">All Tweets</h2>
        <p style="text-align: center; color: hsla(0, 0%, 40%, 1.00);">total tweets: <?php echo ""; ?></p>
    </div>
        <p class="php">No tweets yet.</p>
</div>
<script>
        window.addEventListener('message', (event) => {
            // For security, check the origin
            // if (event.origin !== 'https://trusted-domain.com') return;

            if (event.data.success) {
                alert(event.data.success);
            } else if (event.data.error) {
                alert(event.data.error);
            } else if (event.data.message) {
                alert('Received message: ' + event.data.message);
            }
        });
function escapeHtml(text) {
    var map = {
        '&': '&',
        '<': '<',
        '>': '>',
        '"': '"',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function showMessage(text, isError = false) {
    const msg = document.getElementById('message');
    msg.textContent = text;
    msg.className = isError ? 'message error' : 'message success';
    msg.style.display = 'block';
    setTimeout(() => {
        msg.style.display = 'none';
    }, 3000);
}

function loadTweets() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'tweets.php', true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                let tweetsHtml = '';
                data.forEach(tweet => {
                    const profileUrl = 'http://static.amin4fg.com/user_profiles/' + encodeURIComponent(tweet.profile_picture || 'default.png');
                    const date = new Date(tweet.created_at);
                    const formattedDate = date.toLocaleString();
                    tweetsHtml += `<div class="tweet">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <img src="${profileUrl}" class="profile-img" style="width: 40px; height: 40px; margin-right: 10px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <strong>${escapeHtml(tweet.name)}</strong> <span class="muted">(@${escapeHtml(tweet.username)})</span>
                            </div>
                        </div>
                        <p style="margin: 0 0 10px 0; line-height: 1.5;">${escapeHtml(tweet.content)}</p>
                        <small style="position: absolute; top: 10px; right: 10px; color: #b0c4de; font-size: 0.85em;">${escapeHtml(formattedDate)}</small>
                    </div>`;
                });
                document.querySelector('.tweets-header p').textContent = `total tweets: ${data.length}`;
                document.getElementsByClassName('php')[0].innerHTML = tweetsHtml;
            }
        }
    };
    xhr.send();
}

<?php if($user): ?>
document.getElementById('btn').addEventListener('click', () => {
    const content = document.querySelector('textarea[name="content"]').value.trim();
    if (!content) {
        showMessage('Content cannot be empty', true);
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'tweets.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                window.postMessage(response,'*');
                showMessage(response.success);
                document.querySelector('textarea[name="content"]').value = '';
                loadTweets();
            } else {
                const response = JSON.parse(xhr.responseText);
                showMessage(response.error, true);
            }
        }
    };
    xhr.send(JSON.stringify({ content: content }));
});
<?php endif; ?>

// Load tweets on page load
loadTweets();
</script>
    </div>
</body>
</html>