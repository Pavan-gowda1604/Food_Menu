<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "FoodOrderDB");

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["item"], $data["price"], $data["quantity"], $data["total_price"], $data["image"])) {
    echo json_encode(["success" => false, "message" => "Invalid input data"]);
    exit();
}

// Sanitize input data
$item = trim($data["item"]);
$price = floatval($data["price"]);
$quantity = intval($data["quantity"]);
$total_price = floatval($data["total_price"]);
$image = filter_var($data["image"], FILTER_SANITIZE_URL);

// Ensure values are valid
if ($item === "" || $price <= 0 || $quantity <= 0 || $total_price <= 0 || !filter_var($image, FILTER_VALIDATE_URL)) {
    echo json_encode(["success" => false, "message" => "Invalid order details"]);
    exit();
}

// Prepare and execute the SQL statement
$sql = "INSERT INTO orders (item, price, quantity, total_price, image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdiis", $item, $price, $quantity, $total_price, $image);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "order_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Error saving order: " . $conn->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
