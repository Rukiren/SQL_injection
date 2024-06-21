<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopdb";

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchMessage = '';
$sqlQuery = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];

    // 這裡存在 SQL Injection 漏洞
    $sqlQuery = "SELECT * FROM products WHERE id = $search";
    $result = $conn->query($sqlQuery);

    if ($result) {
        if ($result->num_rows > 0) {
            $searchMessage = "<h2>Search Results:</h2><ul>";
            while($row = $result->fetch_assoc()) {
                $searchMessage .= "<li>ID: " . $row['id'] . " - " . $row['name'] . ": " . $row['description'] . " - $" . $row['price'] . "</li>";
            }
            $searchMessage .= "</ul>";
        } else {
            $searchMessage = "No products found.";
        }
    } else {
        $errorMessage = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple Shopping List</title>
</head>
<body>
    <h2>Search Products by ID</h2>
    <form method="get" action="">
        Search by ID: <input type="text" name="search"><br>
        <input type="submit" value="Search">
    </form>
    <p><?php echo $searchMessage; ?></p>

    <h2>Operation Details</h2>
    <p>Last SQL Query: <?php echo htmlspecialchars($sqlQuery); ?></p>
    <p>Error Message: <?php echo htmlspecialchars($errorMessage); ?></p>
</body>
</html>
