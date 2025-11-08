<?php
// Database connection
include "db.php";
include "navbar.php";

// Fetch most sold products
$mostSoldProductsQuery = "
    SELECT id,name, sold
    FROM products
    ORDER BY sold DESC
";
$mostSoldProducts = $conn->query($mostSoldProductsQuery);
if (!$mostSoldProducts) {
    die("Error fetching most sold products: " . $conn->error);
}

// Fetch low stock products
$lowStockProductsQuery = "
    SELECT id,name, quantity,sold
    FROM products
    WHERE quantity-sold <= 50
";
$lowStockProducts = $conn->query($lowStockProductsQuery);
if (!$lowStockProducts) {
    die("Error fetching low stock products: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin-top: 100px;
        }
        .adminContainer {
            width: 95%;
            max-width: 1200px;
            margin: 30px auto;
            margin-top: 150px;  
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
            margin-bottom: 20px;
        }
        .row .card-tab:hover{
            background-color: #f9f9f9;
            transition: background-color 0.3s ease-in-out;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(-5px);
            transition: transform 0.3s ease-in-out;
        }

        .card-tab {
            width: 200px;
            margin-bottom: 20px;
            text-align: center;
            font-weight:bold;
            padding: 20px;
            background-color: white;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }
        
        .tab-content {
            display: none;
            padding: 20px;
            /* background-color: white; */
            border-radius: 10px;
            /* box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); */
        }

        .active-content {
            display: block;
        }

        .list-group {
            list-style-type: none;
            padding: 0;
        }

        .list-group-item {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        /* Table Styling */
    .tableProducts {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 18px;
        text-align: left;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Table Header */
    .tableProducts thead {
        background-color: #3498db;
        color: white;
    }

    .tableProducts thead th {
        padding: 15px;
        font-weight: bold;
        text-transform: uppercase;
    }

    /* Table Body */
    .tableProducts tbody tr {
        border-bottom: 1px solid #ddd;
        transition: background 0.3s ease-in-out;
    }

    .tableProducts tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .tableProducts tbody tr:hover {
        background-color: #d6eaf8;
    }

    /* Table Cells */
    .tableProducts td {
        padding: 12px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .tableProducts {
            font-size: 16px;
        }

        .tableProducts thead {
            display: none;
        }

        .tableProducts tbody, .tableProducts tr, .tableProducts td {
            display: block;
            width: 100%;
        }

        .tableProducts tr {
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
        }

        .tableProducts td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        .tableProducts td::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
    }

    /* Card Tab Active */       

    </style>
</head>
<body>

<div class="adminContainer">
    <h2>Admin Dashboard</h2>

    <!-- Tab Navigation as Cards -->
    <div class="row">
        <div class="card-tab " onclick="showContent('orders', this)">
            <h5>📦 Orders</h5>
        </div>
        <div class="card-tab" onclick="showContent('revenue', this)">
            <h5>💰 Revenue</h5>
        </div>
        <div class="card-tab" onclick="showContent('most-sold', this)">
            <h5>🔥 Most Sold</h5>
        </div>
        <div class="card-tab" onclick="showContent('low-stock', this)">
            <h5>⚠️ Low Stock</h5>
        </div>
    </div>

    <!-- Orders Section -->
    <div id="orders" class=" orders tab-content active-content  ">
        <?php include "adminOrderManage.php"; ?>
    </div>

    <!-- Revenue Section -->
    <div id="revenue" class="tab-content">
        <!-- Include revenue.php or add the revenue-related code here -->
        <?php include "adminRevenue.php"; ?>
    </div>

    <!-- Most Sold Products -->
    <div id="most-sold" class="tab-content">
        <h1>Most Sold Products</h1>
        <table class="tableProducts">
            <thead>
                <tr><th>Product ID</th>
                    <th>Product Name</th>
                    <th>Total Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $mostSoldProducts->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['sold']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Low Stock Products -->
    <div id="low-stock" class="tab-content">
        <h1>Low Stock Products</h1>
        <table class="tableProducts">
            <thead>
                <tr>
                    <th>Product Id</th>
                    <th>Product Name</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $lowStockProducts->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($product['id']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['quantity']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php include "footer.php"; ?>
<?php include "dashboard.php"; ?>

<script>
    function showContent(tabName, element) {
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active-content'));

        // Show the content of the selected tab
        document.getElementById(tabName).classList.add('active-content');

        // Remove active class from all tab buttons
        document.querySelectorAll('.card-tab').forEach(card => card.classList.remove('active-tab'));

        // Add active class to the clicked tab button
        element.classList.add('active-tab');
    }
</script>

</body>
</html>
