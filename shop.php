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
    
    $sqlQuery = "SELECT * FROM products WHERE id = $search";
    
    try {
        $result = $conn->query($sqlQuery);
        if ($result === false) {
            throw new Exception($conn->error);
        }
        if ($result->num_rows > 0) {
            $searchMessage = "<h2>Search Results:</h2><ul>";
            while($row = $result->fetch_assoc()) {
                $searchMessage .= "<li>ID: " . htmlspecialchars($row['id']) . " - " . htmlspecialchars($row['name']) . ": " . htmlspecialchars($row['description']) . " - $" . htmlspecialchars($row['price']) . "</li>";
            }
            $searchMessage .= "</ul>";
        } else {
            $searchMessage = "No products found.";
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
