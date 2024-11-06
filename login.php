<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            header('Location: index.php');
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div style="margin-top:40vh">
        <div class="login_register">
            <h2 style="display:flex;justify-content:center;">LOGIN<h2>
            <form method="POST" action="login.php" style="display:flex; flex-direction:column; margin-inline:500px;">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="add-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>