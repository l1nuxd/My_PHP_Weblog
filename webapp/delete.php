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

$post_id = $_GET['post_id'];

if (!isset($post_id) || !is_numeric($post_id)) {
    header('Location: msg.php?msg=Invalid request&type=error&goto=index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the tweet to check ownership
$stmt = $conn->prepare("SELECT user_id FROM tweets WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$tweet = $result->fetch_assoc();

if (!$tweet) {
    header('Location: msg.php?msg=Tweet not found&type=error&goto=index.php');
    exit();
}

if ($tweet['user_id'] != $user_id) {
    header('Location: msg.php?msg=You can only delete your own tweets&type=error&goto=index.php');
    exit();
}

// Delete the tweet
$stmt_delete = $conn->prepare("DELETE FROM tweets WHERE id = ?");
$stmt_delete->bind_param("i", $post_id);
$stmt_delete->execute();

// Redirect to the profile
header('Location: msg.php?msg=Tweet deleted successfully&type=success&goto=profile.php?user_id=' . $tweet['user_id']);
exit();
?>