<?php
require_once 'sql.php';
session_start();

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

if (!isset($_POST['submit'])) {
    header('Location: panel.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT name, password, profile_picture, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();

$name = trim($_POST['name'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$bio = trim($_POST['bio'] ?? '');

$changed = false;
if ($name !== $current['name']) {
    $changed = true;
}
if ($bio !== ($current['bio'] ?? '')) {
    $changed = true;
}
if (!empty($new_password)) {
    if (password_verify($new_password, $current['password'])) {
        header('Location: msg.php?msg=New password is the same as current password. Please provide a different one.&type=error&goto=panel.php');
        exit();
    } else {
        $changed = true;
    }
}

$profile_picture = $current['profile_picture'] ?: 'default.png';

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !==  UPLOAD_ERR_NO_FILE) {
    if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
        header('Location: msg.php?msg=Error uploading profile picture&type=error&goto=panel.php');
        exit();
    }

    $allowed_extensions = ['image/png', 'image/jpeg', 'image/gif'];
    $original_name = $_FILES['profile_picture']['name'];
    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));


    if (!in_array($_FILES['profile_picture']['type'], $allowed_extensions)) {
        header('Location: msg.php?msg=Invalid profile picture format&type=error&goto=panel.php');
        exit();
    }

    if ($_FILES['profile_picture']['size'] > 400 * 1024) {
        header('Location: msg.php?msg=Profile picture exceeds 400KB&type=error&goto=panel.php');
        exit();
    }

        $upload_dir = '/var/www/html/static/user_profiles/';
        if (!is_dir($upload_dir)) {
            header('Location: msg.php?msg=Upload directory does not exist&type=error&goto=panel.php');
            exit();
        }

        $new_filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
        $destination = $upload_dir . $new_filename;

        if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
            header('Location: msg.php?msg=Failed to save profile picture&type=error&goto=panel.php');
            exit();
        }

        $profile_picture = $new_filename;
        $changed = true;
    }

if (!$changed) {
    header('Location: panel.php');
    exit();
}

if (!empty($new_password)) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET name = ?, password = ?, profile_picture = ?, bio = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $hashed_password, $profile_picture, $bio, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET name = ?, profile_picture = ?, bio = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sssi", $name, $profile_picture, $bio, $user_id);
}

$stmt->execute();

if ($stmt->affected_rows >= 0) {
    header('Location: msg.php?msg=Profile updated successfully&type=success&goto=panel.php');
    exit();
} else {
    header('Location: msg.php?msg=Failed to update profile&type=error&goto=panel.php');
    exit();
}


?>