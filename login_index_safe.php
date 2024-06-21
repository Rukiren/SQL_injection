<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = '';
$registerMessage = '';
$sqlQuery = '';
$errorMessage = '';

// 用户登录
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 使用准备语句防止 SQL 注入
    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($hashedPass);
    $stmt->fetch();

    if ($hashedPass && password_verify($pass, $hashedPass)) {
        $loginMessage = "Login successful!";
    } else {
        $loginMessage = "Invalid username or password.";
    }

    $stmt->close();
}

// 用户注册
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUser = $_POST['new_username'];
    $newPass = $_POST['new_password'];

    // 对密码进行哈希处理
    $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);

    // 使用准备语句防止 SQL 注入
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $newUser, $hashedPass);

    if ($stmt->execute() === TRUE) {
        $registerMessage = "Registration successful!";
    } else {
        $registerMessage = "Error: " . $conn->error;
    }

    $stmt->close();
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
    <p><?php echo htmlspecialchars($loginMessage, ENT_QUOTES, 'UTF-8'); ?></p>

    <h2>Register</h2>
    <form method="post" action="">
        Username: <input type="text" name="new_username"><br>
        Password: <input type="password" name="new_password"><br>
        <input type="submit" name="register" value="Register">
    </form>
    <p><?php echo htmlspecialchars($registerMessage, ENT_QUOTES, 'UTF-8'); ?></p>
</body>
</html>
