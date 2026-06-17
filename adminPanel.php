<?php
session_start();
include "db.php";
include "functions.php";

requireAdmin();

$adminId = $_SESSION['user_id'];
$adminStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$adminStmt->bind_param("i", $adminId);
$adminStmt->execute();
$adminProfile = $adminStmt->get_result()->fetch_assoc();
$adminStmt->close();

ensureMessagingSchema($conn);

$orderStats = $conn->query("
    SELECT
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'pending') AS pending_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'delivered') AS completed_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'cancelled') AS cancelled_orders,
        (SELECT COUNT(*) FROM orders WHERE status = 'ongoing') AS ongoing_orders,
        (SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE status = 'delivered') AS total_revenue
")->fetch_assoc();

$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users WHERE id != 1")->fetch_assoc()['c'];
$totalProducts = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$totalFeedback = $conn->query("SELECT COUNT(*) AS c FROM contact_form")->fetch_assoc()['c'];
$totalIssues = $conn->query("SELECT COUNT(*) AS c FROM issues")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin-dashboard.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="dash-page">
    <h1 class="dash-title">Admin Dashboard</h1>

    <!-- Admin Profile Card -->
    <div class="profile-summary">
        <img src="<?= htmlspecialchars($adminProfile['img'] ?? 'profiles/admin.jpg') ?>" alt="Admin" class="profile-avatar">
        <div class="profile-info">
            <span class="role-badge">Administrator</span>
            <h2><?= htmlspecialchars($adminProfile['first_name'] . ' ' . $adminProfile['last_name']) ?></h2>
            <p><?= htmlspecialchars($adminProfile['email']) ?></p>
            <p class="profile-meta"><?= htmlspecialchars($adminProfile['phone'] ?? 'No phone') ?> · <?= htmlspecialchars($adminProfile['Address'] ?? 'No address') ?></p>
        </div>
        <a href="adminProfile.php" class="profile-edit-btn">Edit Profile →</a>
    </div>

    <!-- Stats Overview -->
    <h3 class="section-label">Overview</h3>
    <div class="stats-grid">
        <div class="stat-card blue"><span class="stat-num"><?= $orderStats['total_orders'] ?></span><span class="stat-lbl">Orders</span></div>
        <div class="stat-card orange"><span class="stat-num"><?= $orderStats['pending_orders'] ?></span><span class="stat-lbl">Pending</span></div>
        <div class="stat-card purple"><span class="stat-num">$<?= number_format($orderStats['total_revenue'], 0) ?></span><span class="stat-lbl">Revenue</span></div>
        <div class="stat-card green"><span class="stat-num"><?= $totalUsers ?></span><span class="stat-lbl">Customers</span></div>
        <div class="stat-card teal"><span class="stat-num"><?= $totalProducts ?></span><span class="stat-lbl">Products</span></div>
        <div class="stat-card pink"><span class="stat-num"><?= $totalFeedback ?></span><span class="stat-lbl">Feedback</span></div>
        <div class="stat-card red"><span class="stat-num"><?= $totalIssues ?></span><span class="stat-lbl">Issues</span></div>
    </div>

    <!-- Quick Links -->
    <h3 class="section-label">Quick Actions</h3>
    <div class="nav-grid">
        <a href="adminOrders.php" class="nav-card">📋 Manage Orders</a>
        <a href="adminAddProduct.php" class="nav-card">📦 Products</a>
        <a href="adminManageUser.php" class="nav-card">👥 Users</a>
        <a href="adminViewFeedback.php" class="nav-card">💬 Feedback</a>
        <a href="adminViewIssues.php" class="nav-card">⚠️ Issues</a>
        <a href="adminProfile.php" class="nav-card">👤 My Profile</a>
    </div>
</div>

<?php include "footer.php"; ?>
<?php include "dashboard.php"; ?>
</body>
</html>
