<?php
session_start();
include 'db.php';
include 'functions.php';

requireCustomer();

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);

    if ($stmt->execute()) {
        echo "Cart updated successfully!";
    } else {
        echo "Error updating cart!";
    }

    $stmt->close();
}

$conn->close();
?>
