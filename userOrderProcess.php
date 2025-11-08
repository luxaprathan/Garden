<?php
include 'db.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION["user_id"];
$cart_id = $_POST['cart_id'] ?? null;
$delivery_date = date('Y-m-d', strtotime('+5 days'));

// If no cart_id is provided, return an error
if (!$cart_id) {
    echo json_encode(["success" => false, "message" => "Invalid cart ID."]);
    exit();
}

// Fetch product details from cart
$sql = "SELECT product_id, quantity, (SELECT current_price FROM products WHERE products.id = cart.product_id) AS price
        FROM cart WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_item = $result->fetch_assoc();
$stmt->close();

// Check if the cart item exists
if ($cart_item) {
    $product_id = $cart_item['product_id'];
    $quantity = $cart_item['quantity'];
    $total_price = $cart_item['price'] * $quantity;

    // Insert into orders
    $insert_order_sql = "INSERT INTO orders (user_id, product_id, delivery_date, total_price, quantity)
                         VALUES (?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($insert_order_sql);
    $order_stmt->bind_param("iisdi", $user_id, $product_id, $delivery_date, $total_price, $quantity);
    if ($order_stmt->execute()) {
        $order_stmt->close();

        // Update the 'sold' column in the products table by adding the order quantity
        $update_sold_sql = "UPDATE products SET sold = sold + ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sold_sql);
        $update_stmt->bind_param("ii", $quantity, $product_id);
        $update_stmt->execute();
        $update_stmt->close();


        // Remove item from cart
        $delete_cart_sql = "DELETE FROM cart WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_cart_sql);
        $delete_stmt->bind_param("i", $cart_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Return success response
        echo json_encode(["success" => true]);
    } else {
        // Handle error during order insertion
        echo json_encode(["success" => false, "message" => "Failed to place order."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Cart item not found."]);
}

$conn->close();
?>
