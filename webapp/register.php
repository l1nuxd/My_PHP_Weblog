<?php
require_once 'sql.php';

if (isset($_POST['submit'])) {

    $stmt = $conn->prepare("SELECT * FROM invitation_codes WHERE invitation_code = ? AND used = 0");
    $stmt->bind_param("s", $_POST['invitation_code']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Invalid or already used invitation code");
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Email already registered");
    }

    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("Username already taken");
    }

    //hashed password
    $hashed_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $_POST['name'], $_POST['username'], $_POST['email'], $hashed_password);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt = $conn->prepare("UPDATE invitation_codes SET used = 1 WHERE invitation_code = ?");
        $stmt->bind_param("s", $_POST['invitation_code']);
        $stmt->execute();
        header('Location: msg.php?msg=Your account registered successfully!&type=success&goto=login.php');
        exit();
    }
    else {
        header('Location: msg.php?msg=Registration failed, please try again.&type=error&goto=register.php');
        exit();
    }    
        
} else {

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="statics/styles.css">
    <title>Register</title>
</head>
<body>
    <form action="register.php" method="POST">
        <h2>Register</h2>
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="invitation_code">Invitation Code:</label>
        <input type="text" id="invitation_code" name="invitation_code" required>
        <a href="login.php">Already have an account? Login here.</a>

        <input type="submit" name="submit" value="Register" class="btn">
    </form>
</body>
</html>
<?php
}
mysqli_close($conn);
?>