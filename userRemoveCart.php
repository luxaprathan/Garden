<?php
include 'db.php';

if (isset($_GET["cart_id"])) {
    $cart_id = intval($_GET["cart_id"]);

    $sql = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: userMyCard.php");
exit();
?>
