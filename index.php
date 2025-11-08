<?php include 'db.php'; ?>
<?php
// Fetch all products
$sql = "SELECT * FROM products WHERE is_on_sale = 1";
$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo "0 results";
}
// Fetch products on sale
$sql_on_sale = "SELECT * FROM products WHERE is_on_sale = 0";
$result_on_sale = $conn->query($sql_on_sale);
$on_sale_products = [];

if ($result_on_sale->num_rows > 0) {
    // Output data of each row
    while ($row = $result_on_sale->fetch_assoc()) {
        $on_sale_products[] = $row;
    }
} else {
    echo "No products on sale";
}
$conn->close();
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
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

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

<!-- Most Popular Product Grid Section -->
<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="Product Image">
                <span class="new-label">NEW</span>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="price">
                    <span class="current-price">$<?php echo number_format($product['current_price'], 2); ?></span>
                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                </p>
                <a href="product.php?product=<?php echo urlencode($product['name']); ?>&image=<?php echo urlencode($product['image']); ?>&price=<?php echo $product['current_price']; ?>">
                    <button class="fg"><a href="login.php" style="text-decoration: none;color: #000;">Add to Card</a></button>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- ON SALES Product Grid Section -->
<div class="tabs">
|<span class="active"> ON SALES</span> |
 
</div>

<div class="product-grid">
    <?php foreach ($on_sale_products as $product): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="Product Image">
                <span class="sale-label">ON SALE</span>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="price">
                    <span class="current-price">$<?php echo number_format($product['current_price'], 2); ?></span>
                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                </p>
                <a href="product.php?product=<?php echo urlencode($product['name']); ?>&image=<?php echo urlencode($product['image']); ?>&price=<?php echo $product['current_price']; ?>">
                    <button class="fg"><a href="login.php" style="text-decoration: none;color: #000;">Add to Card</a></button>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>