<?php
session_start();
include 'db.php';
include 'functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit();
}

if (isAdmin()) {
    echo json_encode(["success" => false, "error" => "Admins cannot add items to cart."]);
    exit();
}

if (!isset($_GET['product_id'])) {
    echo json_encode(["success" => false, "error" => "Product ID is missing."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$product_id = intval($_GET['product_id']);
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

if ($quantity < 1) {
    echo json_encode(["success" => false, "error" => "Quantity must be at least 1."]);
    exit();
}

$check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();
} else {
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true]);
?>
