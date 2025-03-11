<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "FoodOrderDB";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if order_id is provided
if (!isset($_GET["order_id"]) || !is_numeric($_GET["order_id"])) {
    die("Invalid Order ID!");
}

$order_id = $_GET["order_id"];

// Use prepared statement to prevent SQL Injection
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch order details
if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    die("Order not found!");
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        img {
            width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>Order Summary</h2>
    <p><strong>Food Item:</strong> <?php echo htmlspecialchars($order["item"]); ?></p>
    <p><strong>Price:</strong> ₹<?php echo number_format($order["price"], 2); ?></p>
    <p><strong>Quantity:</strong> <?php echo intval($order["quantity"]); ?></p>
    <p><strong>Total Price:</strong> ₹<?php echo number_format($order["total_price"], 2); ?></p>
    <img src="<?php echo htmlspecialchars($order["image"]); ?>" alt="Food Image">
    <p><strong>Order Time:</strong> <?php echo htmlspecialchars($order["order_time"]); ?></p>
</body>
</html>
