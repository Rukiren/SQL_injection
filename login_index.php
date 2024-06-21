<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = '';
$registerMessage = '';
$sqlQuery = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    // 這裡存在 SQL Injection 漏洞
    $sqlQuery = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sqlQuery);

    if ($result->num_rows > 0) {
        $loginMessage = "Login successful!";
    } else {
        $loginMessage = "Invalid username or password.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUser = $_POST['new_username'];
    $newPass = $_POST['new_password'];

    // 插入新用戶
    $sqlQuery = "INSERT INTO users (username, password) VALUES ('$newUser', '$newPass')";
    if ($conn->query($sqlQuery) === TRUE) {
        $registerMessage = "Registration successful!";
    } else {
        $registerMessage = "Error: " . $sqlQuery . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login and Registration Page</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p><?php echo $loginMessage; ?></p>

    <h2>Register</h2>
    <form method="post" action="">
        Username: <input type="text" name="new_username"><br>
        Password: <input type="password" name="new_password"><br>
        <input type="submit" name="register" value="Register">
    </form>
    <p><?php echo $registerMessage; ?></p>

    <h2>Operation Details</h2>
    <p>Last SQL Query: <?php echo $sqlQuery; ?></p>
</body>
</html>
