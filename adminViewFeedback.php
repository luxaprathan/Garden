<?php
// Include the database connection file
include_once "db.php";
include_once "navbar.php";
if (!isset($_SESSION['user_id']) ) {
    header("Location: login.php");
    exit;
}


// Check if a delete request has been made
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];
    
    // Prepare and execute the delete query
    $sqlDelete = "DELETE FROM contact_form WHERE ID = '$deleteID'";
    if ($conn->query($sqlDelete) === TRUE) {
        echo "<script>alert('Message deleted successfully!');</script>";
        // Redirect to refresh the page
        echo "<script>window.location.href = 'adminViewFeedback.php';</script>";
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch messages from the contact table
$sql = "SELECT * FROM contact_form";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
   <link rel="stylesheet" href="css/ad_viewFeetback.css">
   <style>
   </style>
</head>
<body><h1>View Feedback</h1>
<table class="feedback">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Phone Number</th>
            <th>Date & Time</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Check if there are any results
        if ($result->num_rows > 0) {
            // Loop through the results and display each row
            while ($row = $result->fetch_assoc()) {
                // Determine if the message is from a recruiter or a job seeker
        ?>
                <tr>
                    <td><?php echo $row['first_name'] ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a class="delete-btn" href="adminViewFeedback.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                    </td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='8'>No feedback messages found.</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>

</body>
</html>
