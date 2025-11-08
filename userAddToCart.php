<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "User not logged in.";
    exit();
}

// Get product_id from the request
if (isset($_GET['product_id'])&& $_SESSION['user_id']!=1) {
    $user_id = $_SESSION["user_id"];
    $product_id = intval($_GET['product_id']);

    // Check if the product is already in the cart
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if the product already exists in the cart
        $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    } else {
        // Insert the product into the cart
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo json_encode(["success" => true]);
} else {
    echo "Product ID is missing.";
}
?>
