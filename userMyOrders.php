<?php
include 'db.php';
include 'navbar.php';
// session_start();

$user_id = $_SESSION["user_id"];

// Fetch orders from the database
$sql = "SELECT orders.order_id, products.name, products.image, orders.quantity, orders.total_price, orders.order_date, orders.delivery_date, orders.status
        FROM orders 
        INNER JOIN products ON orders.product_id = products.id 
        WHERE orders.user_id = ? 
        ORDER BY orders.order_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="css/order.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    $(".cancel-order").click(function() {
        var orderId = $(this).data("order-id");
        var quantity = $(this).data("quantity");

        // Show the modal
        $("#cancelModal").css("display", "block");

        // Close the modal when the close button is clicked
        $(".close").click(function() {
            $("#cancelModal").css("display", "none");
        });

        // Cancel the order if "Yes, Cancel Order" is clicked
        $("#confirmCancel").click(function() {
            $.ajax({
                url: "userCancelOrder.php",
                type: "POST",
                data: { order_id: orderId,quantity:quantity },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === "success") {
                        location.reload();
                    } else {
                        alert("Failed to cancel order.");
                    }
                }
            });
            $("#cancelModal").css("display", "none"); // Hide the modal after action
        });

        // Close the modal without any action if "No, Keep Order" is clicked
        $("#closeModal").click(function() {
            $("#cancelModal").css("display", "none");
        });
    });
});

</script>
</head>
<body>
<h2>My Orders</h2>

<?php if (!empty($orders)): ?>
    <?php foreach ($orders as $order): ?>
        <?php
        // Fetch user details
        $user_sql = "SELECT phone, Address FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        $user_details = $user_result->fetch_assoc();
        ?>
        <div class="accordion">
            <div class="order-header" onclick="toggleOrderDetails(this)">
                <div>Order ID: #<?php echo 1000000 + $order['order_id']; ?></div>
                <div>Product: <?php echo $order['name']; ?></div>
                <div>Status: <span class="status <?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></div>
                <button class="toggle-btn">+</button>
            </div>

            <div class="order-details">
                <h2>Order Summary</h2>
                <div class="order-info">
                    <div class="order-products">
                        <img src="<?php echo $order['image']; ?>" alt="Product Image" class="order-img">
                        <div class="product-info">
                            <h3>Ordered Products</h3>
                            <p><strong><?php echo $order['name']; ?></strong></p>
                            <p><strong>Quantity:</strong>  <?php echo $order['quantity']; ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($order['total_price']/$order['quantity'], 2); ?></p>
                            <p><strong>Subtotal:</strong>$<?php echo number_format($order['total_price'], 2); ?></p>
                            <p><strong>OrderDate:</strong><?php echo date('M-d-Y', strtotime($order['order_date'])); ?></p>
                        </div>

                        <div class="shipping-info">
                            <h3>Shipping Details</h3>
                            <p><strong>Name:</strong> John Doe</p>
                            <p><strong>Address:</strong><?php echo $user_details['Address']; ?></p>
                            <p><strong>Contact:</strong> <?php echo $user_details['phone']; ?></p>
                            <p><strong>Estimated Delivery:</strong> <?php echo date('M-d-Y', strtotime($order['delivery_date']))."(".date('D', strtotime($order['delivery_date'])).")"; ?></p>
                        </div>

                        <div class="payment-info">
                            <h3>Payment Details</h3>
                            <p><strong>Payment Method:</strong>  <?php echo $order['payment_method'] ?? 'Cash on Delivery'; ?></p>
                            <p><strong>Transaction ID:</strong><?php echo 1000000 + $order['order_id']; ?></p>
                            <p><strong>Payment Status:</strong><?php echo $order['status'] == 'cancelled' ? 'No Paid' : ($order['status'] == 'pending' ? 'pending' : 'paid'); ?>
                            </p>
                            <!-- <button class="cancel-btn">Cancel Order</button> -->
                            <?php 
                           if ($order['status'] == 'pending' || $order['status'] == 'progress') {
                            echo "<div class='cancel-container'>
                                    <button class='cancel-order' data-order-id='" . $order['order_id'] . "' data-quantity='" . $order['quantity'] . "'>Cancel</button>
                                  </div>";
                            }
                        
                            ?>

                        </div>
                        
                    </div>
                </div>
            </div>
            <?php $user_stmt->close(); ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="empty-orders" style="margin-bottom:100px;margin-top:100px">
        You have no orders yet. <a href="home.php">Browse Products</a>
    </p>
<?php endif; ?>



<?php include_once "model.php"; ?>
<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>

<script>
    function toggleOrderDetails(header) {
        const details = header.nextElementSibling;
        const btn = header.querySelector(".toggle-btn");
        
        if (details.style.display === "block") {
            details.style.display = "none";
            btn.textContent = "+";
        } else {
            details.style.display = "block";
            btn.textContent = "-";
        }
    }
    
</script>

</body>
</html>
