<?php
session_start();
include_once "db.php";
include_once "functions.php";

requireAdmin();

$formError = "";
$formSuccess = "";

if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);

    $cartStmt = mysqli_prepare($conn, "DELETE FROM cart WHERE product_id = ?");
    mysqli_stmt_bind_param($cartStmt, "i", $deleteId);
    mysqli_stmt_execute($cartStmt);
    mysqli_stmt_close($cartStmt);

    $orderStmt = mysqli_prepare($conn, "UPDATE orders SET product_id = NULL WHERE product_id = ?");
    mysqli_stmt_bind_param($orderStmt, "i", $deleteId);
    mysqli_stmt_execute($orderStmt);
    mysqli_stmt_close($orderStmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $deleteId);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: adminAddProduct.php?deleted=1");
        exit;
    }
    $formError = "Failed to delete product. Please try again.";
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addProduct'])) {
    $name = trim($_POST['name']);
    $current_price = $_POST['current_price'];
    $original_price = $_POST['original_price'];
    $quantity = $_POST['quantity'];
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;

    $priceError = validatePrice($current_price);
    $origPriceError = validatePrice($original_price);
    $qtyError = validateQuantity($quantity);

    if (empty($name)) {
        $formError = "Product name is required.";
    } elseif ($priceError) {
        $formError = $priceError;
    } elseif ($origPriceError) {
        $formError = $origPriceError;
    } elseif ($qtyError) {
        $formError = $qtyError;
    } elseif (empty($_FILES['image']['name'])) {
        $formError = "Product image is required.";
    } else {
        $image = $_FILES['image']['name'];
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

        $query = "INSERT INTO products (name, image, current_price, original_price, is_on_sale, quantity) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssddii", $name, $target_file, $current_price, $original_price, $is_on_sale, $quantity);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: adminAddProduct.php?added=1");
            exit;
        }
        $formError = "Failed to add product. Please try again.";
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProduct'])) {
    $id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $current_price = $_POST['current_price'];
    $original_price = $_POST['original_price'];
    $quantity = $_POST['quantity'];
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;

    $priceError = validatePrice($current_price);
    $origPriceError = validatePrice($original_price);
    $qtyError = validateQuantity($quantity);

    if (empty($name)) {
        $formError = "Product name is required.";
    } elseif ($priceError) {
        $formError = $priceError;
    } elseif ($origPriceError) {
        $formError = $origPriceError;
    } elseif ($qtyError) {
        $formError = $qtyError;
    } else {
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

            $query = "UPDATE products SET name=?, image=?, current_price=?, original_price=?, is_on_sale=?, quantity=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssddiii", $name, $target_file, $current_price, $original_price, $is_on_sale, $quantity, $id);
        } else {
            $query = "UPDATE products SET name=?, current_price=?, original_price=?, is_on_sale=?, quantity=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sddiii", $name, $current_price, $original_price, $is_on_sale, $quantity, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            header("Location: adminAddProduct.php?updated=1");
            exit;
        }
        $formError = "Failed to update product.";
        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['added'])) {
    $formSuccess = "Product added successfully!";
} elseif (isset($_GET['updated'])) {
    $formSuccess = "Product updated successfully!";
} elseif (isset($_GET['deleted'])) {
    $formSuccess = "Product deleted successfully!";
}

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .products-page {
            max-width: 1200px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .toolbar h1 {
            margin: 0;
            font-size: 26px;
            color: #0a0a23;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #productFilter {
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            width: 220px;
        }

        .btn-add {
            padding: 12px 22px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(52,152,219,0.35);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-error { background: #fdecea; color: #c0392b; border: 1px solid #e74c3c; }
        .alert-success { background: #eafaf1; color: #27ae60; border: 1px solid #2ecc71; }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .product-card-image {
            position: relative;
            height: 180px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .product-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sale-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #e74c3c;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .product-card-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-card-body h3 {
            margin: 0 0 8px;
            font-size: 16px;
            color: #333;
        }

        .product-prices {
            margin-bottom: 10px;
        }

        .current-price {
            font-size: 18px;
            font-weight: 700;
            color: #27ae60;
        }

        .original-price {
            font-size: 14px;
            color: #999;
            text-decoration: line-through;
            margin-left: 8px;
        }

        .product-meta {
            display: flex;
            gap: 12px;
            font-size: 13px;
            color: #666;
            margin-bottom: 14px;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .btn-update, .btn-delete {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-update {
            background: #3498db;
            color: white;
        }

        .btn-update:hover { background: #2980b9; }

        .btn-delete {
            background: #fff;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .btn-delete:hover {
            background: #e74c3c;
            color: white;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #888;
            background: #fff;
            border-radius: 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal.open { display: flex; }

        .modal-content {
            background: #fff;
            padding: 28px;
            border-radius: 12px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-content h3 {
            margin: 0 0 20px;
            color: #0a0a23;
        }

        .close {
            position: absolute;
            top: 14px; right: 18px;
            font-size: 26px;
            cursor: pointer;
            color: #999;
            line-height: 1;
        }

        .close:hover { color: #333; }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.15);
        }

        .form-row {
            display: flex;
            gap: 12px;
        }

        .form-row .form-group { flex: 1; }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .checkbox-group input { width: 18px; height: 18px; accent-color: #3498db; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-submit:hover { opacity: 0.95; }

        .field-error { color: #e74c3c; font-size: 12px; display: block; margin-top: 4px; }
        .input-error { border-color: #e74c3c !important; }

        .hint { font-size: 12px; color: #888; margin-top: 4px; }
    </style>
</head>
<body>

<?php include_once "navbar.php"; ?>

<div class="products-page">
    <div class="toolbar">
        <h1>Products</h1>
        <div class="toolbar-actions">
            <input type="text" id="productFilter" placeholder="Search products..." onkeyup="filterProducts()">
            <button type="button" class="btn-add" onclick="openAddModal()">+ Add Product</button>
        </div>
    </div>

    <?php if ($formError): ?>
        <div class="alert alert-error"><?= htmlspecialchars($formError) ?></div>
    <?php endif; ?>
    <?php if ($formSuccess): ?>
        <div class="alert alert-success"><?= htmlspecialchars($formSuccess) ?></div>
    <?php endif; ?>

    <div class="product-grid" id="productGrid">
        <?php if (count($products) === 0): ?>
            <div class="empty-state">
                <p>No products yet. Click <strong>Add Product</strong> to create one.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-name="<?= strtolower(htmlspecialchars($product['name'])) ?>">
                    <div class="product-card-image">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php if ($product['is_on_sale']): ?>
                            <span class="sale-tag">ON SALE</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-card-body">
                        <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="product-prices">
                            <span class="current-price">$<?= number_format($product['current_price'], 2) ?></span>
                            <span class="original-price">$<?= number_format($product['original_price'], 2) ?></span>
                        </div>
                        <div class="product-meta">
                            <span>Stock: <?= (int)$product['quantity'] ?></span>
                            <span>Sold: <?= (int)$product['sold'] ?></span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-update" onclick='openEditModal(<?= json_encode([
                                "id" => $product["id"],
                                "name" => $product["name"],
                                "current_price" => $product["current_price"],
                                "original_price" => $product["original_price"],
                                "quantity" => $product["quantity"],
                                "is_on_sale" => $product["is_on_sale"],
                                "image" => $product["image"]
                            ], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Update</button>
                            <button type="button" class="btn-delete"
                                data-id="<?= (int)$product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                onclick="deleteProduct(this)">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add Product</h3>
        <form method="POST" enctype="multipart/form-data" data-validate>
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="Enter product name" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Current Price ($)</label>
                    <input type="number" name="current_price" step="0.01" min="0" data-rule="price" required>
                </div>
                <div class="form-group">
                    <label>Original Price ($)</label>
                    <input type="number" name="original_price" step="0.01" min="0" data-rule="price" required>
                </div>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="quantity" min="0" data-rule="quantity" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" name="is_on_sale" id="add_is_on_sale">
                <label for="add_is_on_sale">Mark as On Sale</label>
            </div>
            <button type="submit" name="addProduct" class="btn-submit">Add Product</button>
        </form>
    </div>
</div>

<!-- Update Product Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Update Product</h3>
        <form method="POST" enctype="multipart/form-data" data-validate>
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*">
                <p class="hint">Leave empty to keep current image</p>
                <img id="edit_image_preview" src="" alt="Current" style="max-width:80px;margin-top:8px;border-radius:6px;display:none;">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Current Price ($)</label>
                    <input type="number" name="current_price" id="edit_current_price" step="0.01" min="0" data-rule="price" required>
                </div>
                <div class="form-group">
                    <label>Original Price ($)</label>
                    <input type="number" name="original_price" id="edit_original_price" step="0.01" min="0" data-rule="price" required>
                </div>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="quantity" id="edit_quantity" min="0" data-rule="quantity" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" name="is_on_sale" id="edit_is_on_sale">
                <label for="edit_is_on_sale">On Sale</label>
            </div>
            <button type="submit" name="updateProduct" class="btn-submit">Update Product</button>
        </form>
    </div>
</div>

<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>

<script src="js/validation.js"></script>
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.add('open');
    }

    function openEditModal(product) {
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_current_price').value = product.current_price;
        document.getElementById('edit_original_price').value = product.original_price;
        document.getElementById('edit_quantity').value = product.quantity;
        document.getElementById('edit_is_on_sale').checked = product.is_on_sale == 1;

        var preview = document.getElementById('edit_image_preview');
        if (product.image) {
            preview.src = product.image;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }

        document.getElementById('editModal').classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function deleteProduct(btn) {
        var id = btn.getAttribute('data-id');
        var name = btn.getAttribute('data-name');
        if (confirm('Are you sure you want to delete "' + name + '"?\n\nThis action cannot be undone.')) {
            window.location.href = 'adminAddProduct.php?delete_id=' + id;
        }
    }

    function filterProducts() {
        var filter = document.getElementById('productFilter').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(function(card) {
            var name = card.getAttribute('data-name') || '';
            card.style.display = name.includes(filter) ? '' : 'none';
        });
    }

    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('open');
            }
        });
    });

    <?php if ($formError && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProduct'])): ?>
        openAddModal();
    <?php elseif ($formError && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProduct'])): ?>
        document.getElementById('editModal').classList.add('open');
    <?php endif; ?>
</script>
</body>
</html>
