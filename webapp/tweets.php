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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            http_response_code(401);
            echo json_encode(['error' => 'Session expired']);
            exit();
        } else {
            header('Location: login.php');
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle tweet submission
    $input = json_decode(file_get_contents('php://input'), true);
    $content = trim($input['content'] ?? '');

    if (!isset($_SESSION['login'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit();
    }

    if (empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Content cannot be empty']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO tweets (user_id, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $_SESSION['user_id'], $content);
    if ($stmt->execute()) {
        echo json_encode(['success' => 'Tweet submitted successfully!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to submit tweet']);
    }
    exit();
} 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all tweets
    $stmt_tweets = $conn->prepare("SELECT tweets.*, users.username, users.name, users.profile_picture FROM tweets JOIN users ON tweets.user_id = users.id ORDER BY tweets.created_at DESC");
    $stmt_tweets->execute();
    $result_tweets = $stmt_tweets->get_result();
    $tweets = [];
    while ($row = $result_tweets->fetch_assoc()) {
        $tweets[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($tweets);
}
?>