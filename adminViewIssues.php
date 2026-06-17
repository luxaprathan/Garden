<?php
session_start();
include_once "db.php";
include_once "functions.php";

requireAdmin();
ensureMessagingSchema($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $issueId = intval($_POST['issue_id']);
    $status = $_POST['status'];
    $allowed = ['pending', 'in_progress', 'resolved', 'rejected'];

    if (in_array($status, $allowed)) {
        $stmt = $conn->prepare("UPDATE issues SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $issueId);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: adminViewIssues.php?updated=1");
    exit;
}

if (isset($_GET['delete_id'])) {
    $deleteID = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM issues WHERE id = ?");
    $stmt->bind_param("i", $deleteID);
    if ($stmt->execute()) {
        header("Location: adminViewIssues.php?deleted=1");
        exit;
    }
    $stmt->close();
}

$sql = "SELECT * FROM issues ORDER BY reported_at DESC";
$result = $conn->query($sql);
$issueCount = $result ? $result->num_rows : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Issues</title>
    <link rel="stylesheet" href="css/ad_viewFeetback.css">
    <style>
        body {
            padding-top: 100px;
        }

        .issues-header {
            text-align: center;
            margin: 30px 0 10px;
        }

        .issues-header p {
            color: #666;
            margin-top: 8px;
        }

        .issue-count {
            display: inline-block;
            background: #e67e22;
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

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #f39c12;
            color: #fff;
        }

        .status-in-progress {
            background: #3498db;
            color: #fff;
        }

        .status-resolved {
            background: #27ae60;
            color: #fff;
        }

        .status-rejected {
            background: #e74c3c;
            color: #fff;
        }

        .status-form select {
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-size: 13px;
        }

        .status-form button {
            padding: 6px 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 4px;
        }
    </style>
</head>

<body>

    <?php include_once "navbar.php"; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert-banner">Issue deleted successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert-banner">Issue status updated successfully.</div>
    <?php endif; ?>

    <div class="issues-header">
        <h1>Reported Issues <?= $issueCount > 0 ? '<span class="issue-count">' . $issueCount . '</span>' : '' ?></h1>
        <p>Issues submitted from the Contact Us page</p>
    </div>

    <table class="feedback">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Issue</th>
                <th>Status</th>
                <th>Date & Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $status = $row['status'] ?? 'pending';
                    $statusClass = issueStatusClass($status);
                    $statusLabel = issueStatusLabel($status);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['issue']) ?></td>
                        <td>
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            <form method="POST" class="status-form" style="margin-top:6px;">
                                <input type="hidden" name="issue_id" value="<?= (int)$row['id'] ?>">
                                <select name="status">
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($row['reported_at']) ?></td>
                        <td>
                            <a class="delete-btn" href="adminViewIssues.php?delete_id=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this issue?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No issues reported yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php include_once "footer.php"; ?>
    <?php include_once "dashboard.php"; ?>
</body>

</html>