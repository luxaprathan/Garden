<?php
// session_start();
include_once "navbar.php";
include_once "db.php";
if (!isset($_SESSION['user_id']) ) {
    header("Location: login.php");
    exit;
}
// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    echo "Please log in as admin to access this page.";
    exit;
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addProduct'])) {
    $name = $_POST['name'];
    $current_price = $_POST['current_price'];
    $original_price = $_POST['original_price'];
    $quantity = $_POST['quantity'];
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    
    // Image upload handling
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    $query = "INSERT INTO products (name, image, current_price, original_price, is_on_sale,quantity) VALUES (?, ?, ?, ?,?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssddii", $name, $target_file, $current_price, $original_price, $is_on_sale,$quantity);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo "<script>showModal('Product added successfully!');</script>";
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProduct'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $current_price = $_POST['current_price'];
    $original_price = $_POST['original_price'];
    $quantity = $_POST['quantity'];
    $is_on_sale = isset($_POST['is_on_sale']) ? 1 : 0;
    
    $query = "UPDATE products SET name=?, current_price=?, original_price=?, is_on_sale=?,quantity=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sddiii", $name, $current_price, $original_price, $is_on_sale,$quantity, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo "<script>showModal('Product updated successfully!');</script>";
}

// Fetch all products
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>
<title>Products</title>
<style>
            /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h2 {
            text-align: center;
            margin-bottom:10px;
            margin-top: 80px;
            color: #333;
        }

        /* Form Styles */
        #product {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="number"], input[type="file"], input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            padding: 10px 15px;
            background-color: #007BFF; /* Blue color */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            margin-left: auto;
            margin-right: auto; /* Horizontally centers the table */
            width: 80%; /* Adjust the width as needed */
            border-collapse: collapse;
            border-radius: 10px 10px 10px 10px;
            background-color: #fff;
            border:none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
        }
        th, td {
            padding: 15px;
            text-align: center;
            border:white;
            border-bottom: 1px solid #ddd;
            transition:  0.3s;
        }
        th {
            background-color: #007BFF; /* Blue color */
            color: white;
        }

        td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        /* Action Button Styles */
        button {
            padding: 6px 12px;
            background-color: #007BFF; /* Blue color */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal input[type="text"], .modal input[type="number"], .modal input[type="file"], .modal input[type="checkbox"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal button[type="submit"] {
            padding: 10px 15px;
            background-color: #007BFF; /* Blue color */
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .modal button[type="submit"]:hover {
            background-color: #0056b3;
        }
        #productFilter{
            width:50%;
            margin-top:10px;
            margin-bottom:20px;
            margin-left: 25%;
            border:none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

 </style>

<h2>Add Product</h2>
<form method="POST" enctype="multipart/form-data" id="product">
    <label>Name:</label>
    <input type="text" name="name" required><br>
    <label>Image:</label>
    <input type="file" name="image" required><br>
    <label>Current Price:</label>
    <input type="number" name="current_price" step="0.01" required><br>
    <label>Original Price:</label>
    <input type="number" name="original_price" step="0.01" required><br>
    <label>Quantity:</label>
    <input type="number" name="quantity" required><br>
    <label>On Sale:</label>
    <input type="checkbox" name="is_on_sale"><br>
    <button type="submit" name="addProduct">Add Product</button>
</form>

<h2>Product List</h2>

<!-- Filter Input -->
<input type="text" id="productFilter" placeholder="Search products..." onkeyup="filterProducts()">

<table border="1" id="productTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Image</th>
            <th>Current Price</th>
            <th>Original Price</th>
            <th>Quantity</th>
            <th>Sold</th>
            <th>On Sale</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td class="product-name"><?php echo htmlspecialchars($row['name']); ?></td>
                <td><img src="<?php echo $row['image']; ?>" width="50"></td>
                <td><?php echo htmlspecialchars($row['current_price']); ?></td>
                <td><?php echo htmlspecialchars($row['original_price']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['sold']); ?></td>
                <td><?php echo $row['is_on_sale'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <button onclick="editProduct(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', <?php echo $row['current_price']; ?>, <?php echo $row['original_price']; ?>, <?php echo $row['quantity']; ?>, <?php echo $row['is_on_sale']; ?>)">Edit</button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Edit Modal -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Edit Product</h3>
        <form method="POST">
            <input type="hidden" name="product_id" id="product_id">
            <label>Name:</label>
            <input type="text" name="name" id="edit_name" required><br>
            <label>Current Price:</label>
            <input type="number" name="current_price" id="edit_current_price" step="0.01" required><br>
            <label>Original Price:</label>
            <input type="number" name="original_price" id="edit_original_price" step="0.01" required><br>
            <label>Quantity:</label>
            <input type="number" name="quantity" id="quantity" required><br>
            <label>On Sale:</label>
            <input type="checkbox" name="is_on_sale" id="edit_is_on_sale"><br>
            <button type="submit" name="updateProduct">Update Product</button>
        </form>
    </div>
</div>

<?php include_once "footer.php";?>
<?php include_once "dashboard.php";?>

<script>
    function editProduct(id, name, currentPrice, originalPrice, quantity, isOnSale) {
        document.getElementById('product_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_current_price').value = currentPrice;
        document.getElementById('edit_original_price').value = originalPrice;
        document.getElementById('quantity').value = quantity;
        document.getElementById('edit_is_on_sale').checked = isOnSale == 1;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function showModal(message) {
        alert(message);
    }

    function filterProducts() {
        var filter = document.getElementById("productFilter").value.toLowerCase();
        var table = document.getElementById("productTable");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1; i < rows.length; i++) {
            var nameCell = rows[i].getElementsByClassName("product-name")[0];
            if (nameCell) {
                var nameText = nameCell.textContent || nameCell.innerText;
                if (nameText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
</script>
