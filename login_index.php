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
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    $sqlQuery = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    
    try {
        $result = $conn->query($sqlQuery);
        if ($result === false) {
            throw new Exception($conn->error);
        }
        if ($result->num_rows > 0) {
            $loginMessage = "Login successful!";
        } else {
            $loginMessage = "Invalid username or password.";
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $newUser = $_POST['new_username'];
    $newPass = $_POST['new_password'];

    $sqlQuery = "INSERT INTO users (username, password) VALUES ('$newUser', '$newPass')";
    
    try {
        if ($conn->query($sqlQuery) === TRUE) {
            $registerMessage = "Registration successful!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
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
    <p><?php echo htmlspecialchars($loginMessage); ?></p>
    
    <h2>Register</h2>
    <form method="post" action="">
        Username: <input type="text" name="new_username"><br>
        Password: <input type="password" name="new_password"><br>
        <input type="submit" name="register" value="Register">
    </form>
    <p><?php echo htmlspecialchars($registerMessage); ?></p>
    
    <h2>Operation Details</h2>
    <p>Last SQL Query: <?php echo htmlspecialchars($sqlQuery); ?></p>
    <p>Error Message: <?php echo htmlspecialchars($errorMessage); ?></p>
</body>
</html>
