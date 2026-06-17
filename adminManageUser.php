<?php

session_start();
include_once "db.php";
include_once "functions.php";
include_once "navbar.php";

requireAdmin();
// Fetch all users from the database
$sql = "SELECT * FROM users WHERE id !=1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage User</title>
    <link rel="stylesheet" href="css/ad_manageUser.css">
    <style>
        .btn-update,
        .btn-delete,
        .btn-view {
            padding: 10px 20px;
            color: white;
            border: none;
            margin: 4px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .btn-view {
            background-color: green;
        }
    </style>
</head>

<body>

    <h1>Manage Users</h1>
    <table>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>NIC</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT ID, first_name, last_Name, NIC,Email,address,dob,gender FROM users ";
        ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['first_name'] . $row['last_name']; ?></td>
                <td><?php echo $row['NIC']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <a class="btn-view" href="adminViewUser.php?id=<?php echo $row['id']; ?>">View</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <?php include_once "footer.php"; ?>
    <?php include_once "dashboard.php"; ?>
</body>

</html>