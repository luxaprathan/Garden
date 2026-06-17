<?php

session_start();
include_once "db.php";
include_once "functions.php";
include_once "navbar.php";

requireAdmin();
// Check if seeker_id is set
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

// Get seeker ID from URL
$id = $_GET['id'];

// Fetch seeker details
$sql = "SELECT * FROM users WHERE id = '$id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if (!$user) {
    die("user not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            /* padding: 20px; */
            /* overflow: hidden; */
        }
       #img {
            width: 200px; /* Adjust the width as needed */
            height: 200px; /* Adjust the height as needed */
            object-fit: cover; /* Ensures the image maintains aspect ratio and fills the box */
            border-radius: 50%; /* Makes the image circular */
            border: 2px solid #ddd; /* Optional: Adds a light border */
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); /* Optional: Adds a soft shadow */
            margin-left:250px;
        }

        h1 {
            margin-top:50px;
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 50px ;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        p {
            /* margin-left:250px; */
            text-align:center;
            font-size: 16px;
            line-height: 1.5;
            color: #555;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-left:35%;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-delete{
            padding: 10px 20px;
            background-color:red;
            color: white;
            border: none;
            margin: 4px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
            cursor: pointer; 
        }
        .btn-delete:hover {
            background-color:crimson;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>User Details</h1>
    <img src="<?php echo $user['img']; ?>" id="img" alt="User Image">
    <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
    <p><strong>NIC:</strong> <?php echo htmlspecialchars($user['NIC']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address']); ?></p>
    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['DOB']); ?></p>
    <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['Gender']); ?></p>

    <a class="btn" href="adminManageUser.php">Back to users</a>
    <a class="btn-delete" href="adminDeleteUser.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
</div>

<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>
</body>
</html>
