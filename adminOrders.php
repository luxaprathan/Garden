<?php
session_start();
include_once "db.php";
include_once "functions.php";

requireAdmin();

$sql = "SELECT orders.order_id, users.first_name AS first_name, users.last_name AS last_name, users.email AS email, users.phone AS phone,
               products.name AS product_name, products.image AS img, orders.quantity AS quantity, orders.total_price AS total_price,
               users.Address AS address, DATE_FORMAT(order_date, '%M %e, %Y') AS order_date,
               DATE_FORMAT(delivery_date, '%M %e') AS delivery_date, orders.status, orders.product_id
        FROM orders
        JOIN users ON orders.user_id = users.id
        JOIN products ON orders.product_id = products.id
        ORDER BY order_id DESC";
$result = $conn->query($sql);

$orderStatsQuery = "
    SELECT
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'pending') AS pending_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'delivered') AS completed_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'cancelled') AS cancelled_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'ongoing') AS ongoing_orders,
        (SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE status = 'delivered') AS total_revenue
";
$orderStatsResult = $conn->query($orderStatsQuery);
$orderStats = $orderStatsResult ? $orderStatsResult->fetch_assoc() : [
    'total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0,
    'cancelled_orders' => 0, 'ongoing_orders' => 0, 'total_revenue' => 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="css/admin-orders.css">
</head>
<body>

<?php include_once "navbar.php"; ?>

<div class="orders-page">
    <div class="page-top">
        <h1>Order Management</h1>
        <a href="adminPanel.php" class="back-link">← Back to Dashboard</a>
    </div>

    <?php include "adminOrderManage.php"; ?>
</div>

<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>
</body>
</html>
<?php $conn->close(); ?>
