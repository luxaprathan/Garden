        <h2 class="section-heading" style="display:block;margin-bottom:16px;color:#0a0a23;">Order Statistics</h2>
        <div class="cardOrder">
            <div class="card cardOrders" style="background-color: #3498db; color: white;" onclick="filterByStatus('all')">
                <div class="card-header">Total Orders</div>
                <div class="card-body"><?= $orderStats['total_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #f39c12; color: white;" onclick="filterByStatus('pending')">
                <div class="card-header">Pending</div>
                <div class="card-body"><?= $orderStats['pending_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #1abc9c; color: white;" onclick="filterByStatus('ongoing')">
                <div class="card-header">Ongoing</div>
                <div class="card-body"><?= $orderStats['ongoing_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #2ecc71; color: white;" onclick="filterByStatus('delivered')">
                <div class="card-header">Delivered</div>
                <div class="card-body"><?= $orderStats['completed_orders'] ?></div>
            </div>
            <div class="card cardOrders" style="background-color: #e74c3c; color: white;" onclick="filterByStatus('cancelled')">
                <div class="card-header">Cancelled</div>
                <div class="card-body"><?= $orderStats['cancelled_orders'] ?></div>
            </div>
        </div>

        <div class="filters">
            <input type="text" id="searchInput" onkeyup="filterOrders()" placeholder="Search by Order ID or Product Name..." class="search-bar">
            <button onclick="filterByDate('today')">Today</button>
            <button onclick="filterByDate('yesterday')">Yesterday</button>
            <button onclick="filterByStatus('pending')">Pending</button>
            <button onclick="filterByStatus('ongoing')">Ongoing</button>
            <button onclick="filterByStatus('all')">All</button>
        </div>

        <table class="orders-table" id="ordersTable">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Product ID</th>
                    <th>Qty</th>
                    <th>Order Date</th>
                    <th>Delivery</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= 1000000 + $row['order_id'] ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= (int)$row['product_id'] ?></td>
                        <td><?= (int)$row['quantity'] ?></td>
                        <td><?= htmlspecialchars($row['order_date']) ?></td>
                        <td><?= htmlspecialchars($row['delivery_date']) ?></td>
                        <td>
                        <?php if ($row['status'] == 'pending'): ?>
                            <select class="status-dropdown">
                                <option value="pending" selected>Pending</option>
                                <option value="ongoing">Ongoing</option>
                            </select>
                        <?php elseif ($row['status'] == 'ongoing'): ?>
                            <select class="status-dropdown">
                                <option value="ongoing" selected>Ongoing</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        <?php elseif ($row['status'] == 'delivered'): ?>
                            Delivered
                        <?php else: ?>
                            Cancelled
                        <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="view-button" onclick='viewOrderDetails(<?= json_encode([
                                "order_id" => $row["order_id"],
                                "product_name" => $row["product_name"],
                                "quantity" => $row["quantity"],
                                "order_date" => $row["order_date"],
                                "delivery_date" => $row["delivery_date"],
                                "total_price" => $row["total_price"],
                                "status" => $row["status"],
                                "first_name" => $row["first_name"],
                                "last_name" => $row["last_name"],
                                "address" => $row["address"],
                                "phone" => $row["phone"]
                            ]) ?>)'>View</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

<div class="order-modal" id="orderModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeOrderModal()">✖</span>
        <h3>Order Details</h3>
        <p><strong>Customer:</strong> <span id="modal-customer-name"></span> <span id="modal-customer-last-name"></span></p>
        <p><strong>Address:</strong> <span id="modal-address"></span></p>
        <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
        <p><strong>Order ID:</strong> <span id="modal-order-id"></span></p>
        <p><strong>Product:</strong> <span id="modal-product-name"></span></p>
        <p><strong>Quantity:</strong> <span id="modal-quantity"></span></p>
        <p><strong>Order Date:</strong> <span id="modal-order-date"></span></p>
        <p><strong>Delivery Date:</strong> <span id="modal-delivery-date"></span></p>
        <p><strong>Total Price:</strong> $<span id="modal-total-price"></span></p>
        <p><strong>Status:</strong> <span id="modal-status"></span></p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function viewOrderDetails(order) {
        document.getElementById("modal-customer-name").textContent = order.first_name;
        document.getElementById("modal-customer-last-name").textContent = order.last_name;
        document.getElementById("modal-address").textContent = order.address;
        document.getElementById("modal-phone").textContent = order.phone;
        document.getElementById("modal-order-id").textContent = 1000000 + parseInt(order.order_id);
        document.getElementById("modal-product-name").textContent = order.product_name;
        document.getElementById("modal-quantity").textContent = order.quantity;
        document.getElementById("modal-order-date").textContent = order.order_date;
        document.getElementById("modal-delivery-date").textContent = order.delivery_date;
        document.getElementById("modal-total-price").textContent = order.total_price;
        document.getElementById("modal-status").textContent = order.status;
        document.getElementById("orderModal").style.display = "flex";
    }

    function closeOrderModal() {
        document.getElementById("orderModal").style.display = "none";
    }

    window.onclick = function(event) {
        var modal = document.getElementById('orderModal');
        if (event.target === modal) closeOrderModal();
    };

    $(document).ready(function () {
        $(document).on("change", ".status-dropdown", function () {
            let orderId = $(this).closest("tr").find("td:first").text().trim();
            let newStatus = $(this).val();
            $.ajax({
                url: "adminUpdateStatus.php",
                type: "POST",
                data: { order_id: orderId - 1000000, status: newStatus },
                success: function (response) {
                    try {
                        var result = JSON.parse(response);
                        if (result.status === "success") location.reload();
                        else alert("Failed to update order status.");
                    } catch (e) {
                        alert("Unexpected error. Please try again.");
                    }
                },
                error: function () { alert("Server error. Please try again."); }
            });
        });
    });

    function filterOrders() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let table = document.getElementById("ordersTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let orderId = tr[i].getElementsByTagName("td")[0];
        let productName = tr[i].getElementsByTagName("td")[1];
        
        if (orderId && productName) {
            let orderIdText = orderId.textContent || orderId.innerText;
            let productNameText = productName.textContent || productName.innerText;
            
            if (orderIdText.toLowerCase().includes(input) || productNameText.toLowerCase().includes(input)) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
   
function filterByDate(type) {
    let today = new Date();
    let yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);

    let rows = document.querySelectorAll("#ordersTable tbody tr");
    rows.forEach(row => {
        let orderDate = row.cells[4].innerText.trim(); // Read the formatted date
        let parsedDate = new Date(Date.parse(orderDate)); // Convert to date object

        if ((type === 'today' && parsedDate.toDateString() === today.toDateString()) ||(type === 'yesterday' && parsedDate.toDateString() === yesterday.toDateString())) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

function filterByStatus(status) {
    let rows = document.querySelectorAll("#ordersTable tbody tr");
    rows.forEach(row => {
        let orderStatus = row.cells[6].querySelector('select') ? row.cells[6].querySelector('select').value : row.cells[6].innerText.trim().toLowerCase();

        // If the status is 'all', show all rows
        if (status === 'all') {
            row.style.display = "";
        } else if (orderStatus === status) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

</script>
