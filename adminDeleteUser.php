<?php
include_once "db.php";

// Check if users is set
if (!isset($_GET['id'])) {
    die("User ID not specified.");
}

// Get users ID from URL
$id = $_GET['id'];
// Delete users from the database
$sql = "DELETE FROM users WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    echo "user deleted successfully.";
} else {
    echo "Error deleting user: " . $conn->error;
}

$conn->close();
header("Location: adminManageUser.php"); // Redirect back to manage user page
exit();
?>
