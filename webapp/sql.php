<?php
$host = 'localhost';
$user = 'dbuser';
$pass = 'dbpassword';
$db   = 'Webapp';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('<p style="color:red;">Connection failed: ' . htmlspecialchars(mysqli_connect_error()) . '</p>');
}

// echo '<p style="color:green;">Database connection successful.</p>';

// mysqli_close($conn);
?>