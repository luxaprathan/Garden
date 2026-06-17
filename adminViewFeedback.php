<?php
session_start();
include_once "db.php";
include_once "functions.php";
include_once "navbar.php";

requireAdmin();

if (isset($_GET['delete_id'])) {
    $deleteID = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_form WHERE id = ?");
    $stmt->bind_param("i", $deleteID);
    if ($stmt->execute()) {
        header("Location: adminViewFeedback.php?deleted=1");
        exit();
    }
    $stmt->close();
}

$sql = "SELECT * FROM contact_form ORDER BY created_at DESC";
$result = $conn->query($sql);
$feedbackCount = $result ? $result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link rel="stylesheet" href="css/ad_viewFeetback.css">
    <style>
        body { padding-top: 100px; }
        .feedback-header {
            text-align: center;
            margin: 30px 0 10px;
        }
        .feedback-header p {
            color: #666;
            margin-top: 8px;
        }
        .feedback-count {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            margin-left: 8px;
        }
        .alert-banner {
            max-width: 900px;
            margin: 0 auto 20px;
            padding: 12px;
            background: #eafaf1;
            color: #27ae60;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert-banner">Feedback message deleted successfully.</div>
<?php endif; ?>

<div class="feedback-header">
    <h1>Customer Feedback <?= $feedbackCount > 0 ? '<span class="feedback-count">' . $feedbackCount . '</span>' : '' ?></h1>
    <p>Messages sent from the Contact Us page</p>
</div>

<table class="feedback">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Date & Time</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone_number']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a class="delete-btn" href="adminViewFeedback.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this feedback message?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No feedback messages yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>
</body>
</html>
