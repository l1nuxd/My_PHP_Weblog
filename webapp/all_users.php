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

$stmt = $conn->prepare("SELECT id, username, name, email, profile_picture, created_at, updated_at FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <style>
        .users-table { border-collapse: collapse; border: 1px solid #4b44a9ff; }
        .users-table th, .users-table td { border: 1px solid #447ca9ff; padding: 8px; text-align: center; }
    </style>
    <title>All Users</title>
</head>
<body>
    <div class="panel" style="width: 100%; max-width: none;">
        <div class="panel-header">
            <h1>All Users</h1>
            <a href="index.php">Back to index!</a>
        </div>
        <table class="users-table" style="width: 100%; min-width: 1200px;">
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    $profile_picture = $user['profile_picture'] ?: 'default.png';
                    $profile_picture_url = 'http://static.amin4fg.com/user_profiles/' . rawurlencode($profile_picture);
                    ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($profile_picture_url); ?>" alt="Profile Picture" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;"></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><a href="profile.php?user_id=<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username']); ?></a></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($user['updated_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>