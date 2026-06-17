<?php
session_start();
include "db.php";
include "functions.php";

requireCustomer();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST["order_id"];
    $quantity = $_POST["quantity"];

    // Ensure user is logged in
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit();
    }

    $user_id = $_SESSION["user_id"]; // Get user ID from session

    // Fetch product ID from the order
    $sql = "SELECT product_id FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        $product_id = $order['product_id'];

        // Update the 'sold' column in the products table by subtracting the order quantity
        $update_sold_sql = "UPDATE products SET sold = sold - ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sold_sql);
        $update_stmt->bind_param("ii", $quantity, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Update order status securely using prepared statement
    $query = "UPDATE orders SET status = 'cancelled' WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel order"]);
    }
    
    $stmt->close();
    $conn->close();
}
?>
