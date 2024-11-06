<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username already exists
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Error: Username already exists.";
    } else {
        // Insert the new user
        $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute() === TRUE) {
            header('Location: login.php');
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }

    $stmt->close();
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
            <h2 style="display:flex;justify-content:center;">REGISTER<h2>
            <form method="POST" action="register.php" style="display:flex; flex-direction:column; margin-inline:500px;">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="add-btn">Register</button>
            </form>
        </div>
    </div>
</body>
</html>