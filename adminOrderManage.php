<?php
// Database connection
include "db.php";

// Fetch orders with user and product details
$sql = "SELECT orders.order_id, users.first_name As first_name , users.last_name As last_name , users.email AS email, users.phone as phone, 
               products.name AS product_name, products.image as img, orders.quantity as quantity , orders.total_price as total_price, users.Address AS address, 
               DATE_FORMAT(order_date, '%M %e, %Y') AS order_date, DATE_FORMAT(delivery_date, '%M %e') AS delivery_date, orders.status, orders.product_id 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        JOIN products ON orders.product_id = products.id
        ORDER BY order_id DESC" ;
$result = $conn->query($sql);

// Fetch order statistics
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
if (!$orderStatsResult) {
    die("Error fetching order statistics: " . $conn->error);
}
$orderStats = $orderStatsResult->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <style>
       

        /* Container */
        .filters {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        /* Header */
        h2 {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222;
        }

        /* Filters Section */
        .filters {
            display: flex;
            /* justify-content: space-between; */
            /* flex-wrap: wrap; */
            gap: 12px;
            margin-bottom: 20px;
        }

        .filters .search-bar {
            flex: 0.8;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .filters .search-bar:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .filters button {
            padding: 10px 14px;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .filters button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Orders Table */
        .orders-table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .orders-table th,
        .orders-table td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
        }

        .orders-table th {
            background-color: #007bff;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }

        .orders-table tbody tr {
            transition: background 0.3s ease;
        }

        .orders-table tbody tr:hover {
            background-color: #f9f9f9;
            transition: all 0.2s ease-in-out;
        }

        /* Status Dropdown */
        .status-dropdown {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .status-dropdown:hover {
            border-color: #007bff;
        }

        /* Modal styles */
        .order-modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 300px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            text-align: left;
            position: relative; /* Added to allow positioning of the close icon */
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            color:red;
        }
        ..close-modal:hover {
            font-weight: bold;
        }

        .modal-content h3 {
            font-size: 24px;
            margin-bottom: 15px;
            text-align: center;
        }

        .modal-content p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        /* View Button */
        .view-button {
            padding: 10px 14px;
            background-color: #28a745;
            border: none;
            color: white;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .view-button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .filters {
                flex-direction: column;
                align-items: center;
            }

            .filters .search-bar {
                width: 100%;
            }

            .orders-table {
                width: 90%;
                font-size: 14px;
            }

            .order-sidebar {
                width: 100%;
                max-width: 400px;
            }
        }

        .cardOrder {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: space-around;
            align-items: stretch;
            padding: 20px;
        }

        .cardOrders {
            flex: 1 1 calc(20% - 15px); /* Adjusting for 5 items in a row */
            min-width: 200px;
            max-width: 250px;
            text-align: center;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
           
        }

        .cardOrders:hover {
            transform: scale(1.05);
        
        }

        .card-header {
            font-weight: bold;
            font-size: 18px;
            padding: 10px 0;
        }

        .card-body {
            font-size: 22px;
            font-weight: bold;
            padding: 15px 0;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .cardOrders {
                flex: 1 1 calc(33.33% - 15px); /* 3 items per row */
            }
        }

        @media (max-width: 768px) {
            .cardOrders {
                flex: 1 1 calc(50% - 15px); /* 2 items per row */
            }
        }

        @media (max-width: 480px) {
            .cardOrders {
                flex: 1 1 100%; /* 1 item per row */
            }
        }

    </style>
    
    
</head>
<body>
        <h1>Order Statistics</h1>
        <!-- Order Statistics -->

        <div class="cardOrder">
            <div class="card cardOrders" style="background-color: #3498db; color: white;"onclick="filterByStatus('all')">
                <div class="card-header">Total Orders</div>
                <div class="card-body"><?= $orderStats['total_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #f39c12; color: white;" onclick="filterByStatus('pending')">
                <div class="card-header">Pending Orders</div>
                <div class="card-body"><?= $orderStats['pending_orders']?></div>
            </div>
            <div class="card cardOrders" style="background-color: #1abc9c; color: white;"onclick="filterByStatus('ongoing')">
                <div class="card-header">Ongoing Orders</div>
                <div class="card-body"><?= $orderStats['ongoing_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #2ecc71; color: white;"onclick="filterByStatus('delivered')">
                <div class="card-header">Shipped Orders</div>
                <div class="card-body"><?= $orderStats['completed_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #e74c3c; color: white;"onclick="filterByStatus('cancelled')">
                <div class="card-header">Cancelled Orders</div>
                <div class="card-body"><?= $orderStats['cancelled_orders'] ?></div>
            </div>
        </div>
    <!-- Filters and Search Bar -->
    <div class="filters">
        <input type="text" id="searchInput" onkeyup="filterOrders()" placeholder="Search by Order ID or Product Name..." class="search-bar">
            
        <button onclick="filterByDate('today')">Today</button>
        <button onclick="filterByDate('yesterday')">Yesterday</button>
        <button onclick="filterByStatus('pending')">Pending</button>
        <button onclick="filterByStatus('ongoing')">Ongoing</button>
        <button onclick="filterByStatus('all')">All</button>
    </div>

        <!-- Orders Table -->
        <table class="orders-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Product ID</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= 1000000 + $row['order_id'] ?></td>
                        <td><?= $row['product_name'] ?></td>
                        <td><?= $row['product_id'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['order_date'] ?></td>
                        <td><?= $row['delivery_date'] ?></td>
                        <td>
                        <?php
                            if ($row['status'] == 'pending') {
                            ?>
                                <select class="status-dropdown">
                                    <option value="pending"  default>Pending</option>
                                    <option value="ongoing" >Ongoing</option>
                                </select>
                             <?php   
                            } elseif ($row['status'] == 'ongoing') {
                             ?>
                              <select class="status-dropdown">
                                    <option value="ongoing" default >Ongoing</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            <?php
                            } elseif ($row['status'] == 'delivered') {
                                 echo "Delivered" ;
                            } elseif ( $row['status'] == 'cancelled') {
                                 echo "Cancelled" ;
                            } 
                            ?>
                        </td>
                        <td>
                             <button class="view-button" onclick="viewOrderDetails('<?= addslashes($row['order_id']) ?>', '<?= addslashes($row['product_name']) ?>', '<?= addslashes($row['quantity']) ?>', '<?= addslashes($row['order_date']) ?>', '<?= addslashes($row['delivery_date']) ?>', '<?= addslashes($row['total_price']) ?>', '<?= addslashes($row['status']) ?>','<?= addslashes($row['first_name']) ?>','<?= addslashes($row['last_name']) ?>','<?= addslashes($row['address']) ?>','<?= addslashes($row['phone']) ?>')">View</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    
<!-- Modal Structure -->
<div class="order-modal" id="orderModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">✖</span>
        <h3>Order Details</h3>
        <p><strong>Customer Name:</strong> <span id="modal-customer-name"></span> <span id="modal-customer-last-name"></span></p>
        <p><strong>Address:</strong> <span id="modal-address"></span></p>
        <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
        <p><strong>Order ID:</strong> <span id="modal-order-id"></span></p>
        <p><strong>Product Name:</strong> <span id="modal-product-name"></span></p>
        <p><strong>Quantity:</strong> <span id="modal-quantity"></span></p>
        <p><strong>Order Date:</strong> <span id="modal-order-date"></span></p>
        <p><strong>Delivery Date:</strong> <span id="modal-delivery-date"></span></p>
        <p><strong>Total Price:</strong> <span id="modal-total-price"></span></p>


        <p><strong>Status:</strong> <span id="modal-status"></span></p>
    </div>
</div>



<!-- jQuery CDN -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="admin.js"></script>
        <script>
            // Show modal with order details
            function viewOrderDetails(orderId, productName, quantity, orderDate, deliveryDate, totalPrice, status, firstName, lastName, address, phone) {
                console.log("View Order Clicked:", orderId, productName, quantity, orderDate, deliveryDate, totalPrice, status);
                // Set modal content dynamically
                document.getElementById("modal-customer-name").textContent = firstName;
                
                document.getElementById("modal-customer-last-name").textContent = lastName;
                document.getElementById("modal-address").textContent = address;
                document.getElementById("modal-phone").textContent = phone;
                document.getElementById("modal-order-id").textContent = 1000000 +orderId;
                document.getElementById("modal-product-name").textContent = productName;
                document.getElementById("modal-quantity").textContent = quantity;
                document.getElementById("modal-order-date").textContent = orderDate;
                document.getElementById("modal-delivery-date").textContent = deliveryDate;
                document.getElementById("modal-total-price").textContent = totalPrice;
                document.getElementById("modal-status").textContent = status;

                // Show the modal
                document.getElementById("orderModal").style.display = "flex";
            }

            // Close modal
            function closeModal() {
                document.getElementById("orderModal").style.display = "none";
            }
            // Close modal when clicking outside of modal content
            window.onclick = function(event) {
                var modal = document.getElementById('orderModal');
                var modalContent = document.querySelector('.modal-content');
                // Check if the clicked area is outside of modal content
                if (event.target === modal) {
                    closeModal();
                }
            }
            
            $(document).ready(function () {
                $(document).on("change", ".status-dropdown", function () {
                    let orderId = $(this).closest("tr").find("td:first").text().trim();
                    let newStatus = $(this).val();
                    let row = $(this).closest("tr");

                    $.ajax({
                        url: "adminUpdateStatus.php",
                        type: "POST",
                        data: {
                            order_id: orderId - 1000000, // Adjust order ID
                            status: newStatus
                        },
                        success: function (response) {
                            try {
                                var result = JSON.parse(response);
                                if (result.status === "success") {
                                    location.reload(); // Refresh page on success
                                } else {
                                    alert("Failed to update order status.");
                                }
                            } catch (error) {
                                console.error("JSON Parsing Error:", error, response);
                                alert("Unexpected error. Please try again.");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("AJAX Error:", status, error);
                            alert("Server error. Please try again.");
                        }
                    });
                });
            });
        </script>
        
</body>
</html>
<?php $conn->close(); ?>



