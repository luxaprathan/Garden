<?php
include 'db.php';
include 'navbar.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch cart items
$sql = "SELECT cart.id AS cart_id, products.id AS product_id, products.name, products.image,products.quantity as orginalQuantity,products.sold as soldQuantity, 
               products.current_price, cart.quantity 
        FROM cart 
        INNER JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}
$stmt->close();

// Fetch user details
$user_sql = "SELECT phone, Address FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_details = $user_result->fetch_assoc();
$user_stmt->close();

$delivery_date = date('Y-m-d', strtotime('+5 days'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="css/myCard.css">
    <style>
     /* Modal Background */
        .modal {
            display: none ; /* Ensure it never appears on load */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            opacity: 0; /* Hide it fully */
            transition: opacity 0.3s ease-in-out;
        }



        /* Modal Content */
        .modal-content {
            background: #fff;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
            }

        /* Close Button */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #555;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #ff4b5c; /* Red hover effect */
        }
        .ok-btn {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .ok-btn:hover {
            background: #017bff;
        }
        .modal-content p {
            font-size: 14px;
            color: #444;
            margin-bottom: 10px;
        }
        .modal-content h2 {
            font-size: 20px;
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        /* Modal Animation */
        @keyframes slide-down {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 80%; /* Adjust for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                width: 90%;
                padding: 15px;
            }
            .close-btn {
                font-size: 22px;
                right: 15px;
            }
        }

    </style>
    <script>
        function updateQuantity(cartId, price, action) {
            let quantityInput = document.getElementById('quantity_' + cartId);
            let subtotalSpan = document.getElementById('subtotal_' + cartId);
            let totalSpan = document.getElementById('total_price');

            let currentQuantity = parseInt(quantityInput.value);
            let total = parseFloat(totalSpan.innerText.replace('$', ''));
            let maxQuantity = parseInt(quantityInput.getAttribute("max"));
            if (action === 'increase' && currentQuantity < maxQuantity) {
                currentQuantity++;
            } else if (action === 'decrease' && currentQuantity > 1) {
                currentQuantity--;
            }

            quantityInput.value = currentQuantity;

            // Update subtotal for the product
            let newSubtotal = (currentQuantity * price).toFixed(2);
            subtotalSpan.innerText = `$${newSubtotal}`;

            // Update total price
            let subtotals = document.querySelectorAll('.subtotal');
            let newTotal = 0;
            subtotals.forEach(sub => {
                newTotal += parseFloat(sub.innerText.replace('$', ''));
            });

            totalSpan.innerText = `$${newTotal.toFixed(2)}`;
            
            // Optionally, update the database with the new quantity using AJAX
            updateDatabase(cartId, currentQuantity);
        }

        function updateDatabase(cartId, quantity) {
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "userUpdateCart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(`cart_id=${cartId}&quantity=${quantity}`);
        }
    </script>
    
</head>
<body>
<h2>My Shopping Cart</h2>

<?php if (!empty($cart_items)): ?>
    <table class="cart-table" action="userUpdateCart.php" method="post">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; foreach ($cart_items as $item): 
                $subtotal = $item['current_price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><img src="<?php echo $item['image']; ?>" class="cart-img"></td>
                    <td><?php echo $item['name']; ?></td>
                    <td>$<?php echo number_format($item['current_price'], 2); ?></td>
                    <td>
                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        <div class="quantity-controls">
                            <button type="button" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['current_price']; ?>, 'decrease')">-</button>
                            <input type="number" name="quantity[]" id="quantity_<?php echo $item['cart_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo ($item['orginalQuantity'] - $item['soldQuantity'] >= 10) ? 10 : ($item['orginalQuantity'] - $item['soldQuantity']); ?>" readonly>
                            <button type="button" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['current_price']; ?>, 'increase')">+</button>
                        </div>
                    </td>
                    <td><span id="subtotal_<?php echo $item['cart_id']; ?>" class="subtotal">$<?php echo number_format($subtotal, 2); ?></span></td>
                    <td>
                            <a href="userRemoveCart.php?cart_id=<?php echo $item['cart_id']; ?>" class="remove-btn">Remove</a>
                            <a href="#" class="checkout-single-btn" onclick="showCheckoutModal(<?php echo $item['cart_id']; ?>, <?php echo $subtotal; ?>)">Checkout</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" align="right"><strong>Total:</strong></td>
                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
            </tr>
        </tbody>
    </table>
<?php else: ?>
    <p class="empty-cart"style="margin-bottom:100px;margin-top:100px">Your cart is empty.</p>
<?php endif; ?>


<!-- Checkout Modal -->

<div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeCheckoutModal()">&times;</span>
        <h2>Order Details</h2>
        <p><strong>Phone:</strong> <?php echo $user_details['phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $user_details['Address']; ?></p>
        <p><strong>Delivery Date:</strong> <?php echo $delivery_date; ?></p>
        <p><strong>Total Price:</strong> <span id="total_price"></span></p>
        <button type="button" class="ok-btn" onclick="checkout(),closeCheckoutModal()">OK</button>
    </div>
</div>
<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>

<script>

    function showCheckoutModal(cartId, subtotal) {
        let modal = document.getElementById("checkoutModal");
        modal.style.display = "flex"; // Show the modal
        modal.style.opacity = "1";   // Make the modal visible
        document.getElementById("total_price").innerText = `$${subtotal.toFixed(2)}`;

        // Store the cartId in a global variable for use later
        window.selectedCartId = cartId;
    }

    // Close the checkout modal
    function closeCheckoutModal() {
        let modal = document.getElementById("checkoutModal");
        modal.style.display = "none";  // Hide modal
        modal.style.opacity = "0";    // Hide with opacity fade
    }


    // Ensure modal is not open on page load
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("checkoutModal").style.display = "none";
    });

    function checkout() {
        // Ensure that the cartId is dynamically passed from the modal
        let cartId = window.selectedCartId;

        if (!cartId) {
            alert('No cart item selected!');
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "userOrderProcess.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {  // Request is completed
            if (xhr.status === 200) {  // Response is OK
                try {
                    let response = JSON.parse(xhr.responseText);
                   
                    if (response.success) {
                        location.reload(); // Reload page after successful order
                    } else {
                        alert('Failed to process order.');
                    }
                }catch (error) {
                    alert('Error processing your order.');
                }
            } else {
                console.error('AJAX Error:', xhr.status);  // Log any status errors
                alert('Failed to communicate with the server.');
            }
        }
    };

    xhr.send(`cart_id=${cartId}`); // Send cartId to server for processing
}
</script>
</body>
</html>
