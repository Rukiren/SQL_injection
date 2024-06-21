<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// 連線資料庫
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = '';
$registerMessage = '';
$sqlQuery = '';
$errorMessage = '';

// 用戶登入
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // 使用準備指令防止 SQL Injection
    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($hashedPass);
    $stmt->fetch();

    // 用 password_veritify 驗證 Hash 密碼
    if ($hashedPass && password_verify($pass, $hashedPass)) {
        $loginMessage = "Login successful!";
    } else {
        $loginMessage = "Invalid username or password.";
    }

    $stmt->close();
}

// 註冊
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUser = $_POST['new_username'];
    $newPass = $_POST['new_password'];

    // 將密碼進行 hash 處理
    $hashedPass = password_hash($newPass, PASSWORD_BCRYPT);

    // 使用準備語句防止被 SQL Injection
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
