<?php
require_once 'sql.php';
require_once 'functions.php';
session_start();

$msg = $_GET['msg'];
$type = $_GET['type'];
$goto = $_GET['goto'] ?? 'index.php';

if ($type == "success") {
    $color = "green";
} else {
    $color = "red";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title>Message</title>
</head>
<body>
    <div class="panel">
        <h1>Message</h1>
        <p style="color: <?php echo $color; ?>;"><?php echo htmlspecialchars($msg); ?></p>
        <p>Redirecting to <?php echo $goto; ?> in 1 seconds...</p>
        <script>
            const params = new URLSearchParams(window.location.search);
            const goto = params.get('goto') || 'index.php';
            setTimeout(() => {
                location.href= goto;
            }, 1500);
        </script>
    </div>
</body>
</html>