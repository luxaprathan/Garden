<?php
include 'db.php';
include 'navbar.php'; 

// session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Fetch Regular Products
$sql = "SELECT * FROM products WHERE is_on_sale = 0"; // Regular products
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch On Sale Products
$sql_on_sale = "SELECT * FROM products WHERE is_on_sale = 1"; // On sale products
$result_on_sale = $conn->query($sql_on_sale);
$on_sale_products = [];

if ($result_on_sale->num_rows > 0) {
    while ($row = $result_on_sale->fetch_assoc()) {
        $on_sale_products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero-banner {
            background-image: url('img/home.jpg');
        }
        #productFilter {
            width: 50%;
            margin-top: 10px;
            margin-bottom: 30px;
            border: none;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            width: 300px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Fade In Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

    </style>
</head>
<body>

<!-- Hero Banner Section -->
<div class="hero-banner">
    <div class="hero-content">
        <p class="subtitle">Welcome To My Garden</p>
        <h1>New Collection 2024</h1>
        <a href="#" class="btn">Learn More</a>
    </div>
</div>

<!-- Tabs Section -->
<div class="tabs">
    <span class="active">MOST POPULAR</span> |
    <span>BEST SELLER</span> |
    <span>NEW ARRIVAL</span>
</div>

<!-- Search Filter -->
<input type="text" id="productFilter" placeholder="Search products..." onkeyup="filterProducts()">

<!-- Most Popular Product Grid Section -->
<div class="product-grid" id="productTable">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="Product Image">
                <span class="new-label">NEW</span>
            </div>
            <div class="product-info">
                <h3 class="product-name"><?php echo $product['name']; ?></h3>
                <p class="price">
                    <span class="current-price">$<?php echo number_format($product['current_price'], 2); ?></span>
                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                </p>
                <p class="price">
                    <span class=""><?php echo number_format($product['sold']); ?> Sold</span>
                </p>
                <!-- Add to Cart -->
                <a href="javascript:void(0);" onclick="addToCart(<?php echo $product['id']; ?>)">
                    <button class="fg">Add to Cart 🛒</button>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- On Sale Products Section -->
<div class="tabs">
    <span class="active">ON SALE</span>
</div>

<!-- On Sale Product Grid -->
<div class="product-grid" id="productTable">
    <?php foreach ($on_sale_products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="Product Image">
                <span class="sale-label">ON SALE</span>
            </div>
            <div class="product-info">
                <h3 class="product-name"><?php echo $product['name']; ?></h3>
                <p class="price">
                    <span class="current-price">$<?php echo number_format($product['current_price'], 2); ?></span>
                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                </p>
                <p class="price">
                    <span class=""><?php echo number_format($product['sold']); ?> Sold</span>
                </p>
                <!-- Add to Cart -->
                <a href="javascript:void(0);" onclick="addToCart(<?php echo $product['id']; ?>)">
                    <button class="fg">Add to Cart 🛒</button>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="myModal" class="modal">
        <div class="modal-content">
            <p id="modalText"></p>
        </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'dashboard.php'; ?>

<script>
    function openModal(message) {
            let modal = document.getElementById("myModal");
            let modalText = document.getElementById("modalText");

            modalText.innerText = message; // Set modal text
            modal.style.display = "flex"; // Show modal

            // Automatically close modal after 2 seconds
            setTimeout(() => {
                modal.style.display = "none";
            }, 1000);
        }
    function filterProducts() {
        var filter = document.getElementById("productFilter").value.trim().toLowerCase();
        var grid = document.getElementById("productTable");
        var cards = grid.getElementsByClassName("product-card");

        for (var i = 0; i < cards.length; i++) {
            var nameElement = cards[i].getElementsByClassName("product-name")[0];
            if (nameElement) {
                var nameText = nameElement.textContent || nameElement.innerText;
                cards[i].style.display = nameText.toLowerCase().includes(filter) ? "" : "none";
            }
        }
    }

    // Add to Cart Function (AJAX)
    function addToCart(productId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'userAddToCart.php?product_id=' + productId, true);
        xhr.onreadystatechange = function() {
            if(xhr.readyState == 4 && xhr.status == 200)
            {
                let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        openModal("Product added to cart");
                    }
            }
            
        };
        xhr.send();
    }
</script>

</body>
</html>
