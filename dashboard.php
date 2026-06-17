<?php include_once 'functions.php'; ?>
<link rel="stylesheet" href="css/dashbored.css">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<aside class="sidebar hidden" id="sidebar">
    <div class="user-info">
        <?php if (isUser()): ?>
            <a href="userProfile.php">
                <img src="<?php echo $_SESSION['img']; ?>" alt="User Image">
            </a>
            <a href="userProfile.php" id="name"><?php echo $_SESSION["user_name"]; ?></a>
        <?php elseif (isAdmin()): ?>
            <a href="adminProfile.php">
                <img src="<?php echo $_SESSION['img']; ?>" alt="Image">
            </a>
            <a href="adminProfile.php" id="name"><?php echo $_SESSION["user_name"]; ?></a>
        <?php endif; ?>
    </div>

    <nav class="navigation">
        <h3><i class='bx bxs-dashboard'></i> Menu</h3>
        <br>
        <div class="dashmenu">
            <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
            <?php if (isUser()): ?>
                <a href="home.php"> Home</a>
                <a href="userMyCard.php"> My Cart</a>
                <a href="userMyOrders.php"> My Orders</a>
                <a href="userProfile.php" class="<?= $currentPage === 'userProfile.php' ? 'active' : '' ?>"> My Profile</a>
            <?php elseif (isAdmin()): ?>
                <a href="adminPanel.php" class="<?= $currentPage === 'adminPanel.php' ? 'active' : '' ?>"> Dashboard</a>
                <a href="adminOrders.php" class="<?= $currentPage === 'adminOrders.php' ? 'active' : '' ?>"> Manage Orders</a>
                <a href="adminAddProduct.php" class="<?= $currentPage === 'adminAddProduct.php' ? 'active' : '' ?>"> Products</a>
                <a href="adminManageUser.php" class="<?= $currentPage === 'adminManageUser.php' ? 'active' : '' ?>"> Users</a>
                <a href="adminViewFeedback.php" class="<?= $currentPage === 'adminViewFeedback.php' ? 'active' : '' ?>"> Feedback</a>
                <a href="adminViewIssues.php" class="<?= $currentPage === 'adminViewIssues.php' ? 'active' : '' ?>"> Issues</a>
                <a href="adminProfile.php" class="<?= $currentPage === 'adminProfile.php' ? 'active' : '' ?>"> My Profile</a>
            <?php endif; ?>
        </div>
    </nav>
    <img src="img/logo.png" class="footer_logo" alt="Logo" style="margin-left:35px;margin-top:10px;width: 200px;">
</aside>

<button id="menu-btn" type="button">
    <i class='bx bx-menu-alt-left'></i>
</button>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var sidebar = document.getElementById("sidebar");
    var menuBtn = document.getElementById("menu-btn");
    if (!sidebar || !menuBtn) return;

    menuBtn.addEventListener("click", function () {
        sidebar.classList.toggle("hidden");
        if (sidebar.classList.contains("hidden")) {
            menuBtn.style.left = "0px";
            document.body.style.marginLeft = "0";
            menuBtn.style.padding = "10px 15px 15px 10px";
        } else {
            menuBtn.style.left = "250px";
            menuBtn.style.padding = "0";
            document.body.style.marginLeft = "298px";
        }
        var menuIcon = menuBtn.querySelector("i");
        menuIcon.classList.toggle("bx-menu-alt-left");
        menuIcon.classList.toggle("bx-menu-alt-right");
    });
});

var lastScrollTop = 0;
window.addEventListener("scroll", function () {
    var sidebar = document.getElementById("sidebar");
    var menuBtn = document.getElementById("menu-btn");
    if (!sidebar || !menuBtn) return;
    var currentScroll = window.scrollY;
    if (currentScroll > lastScrollTop) {
        sidebar.classList.add("hidden");
        document.body.style.marginLeft = "0";
        menuBtn.style.left = "0px";
        menuBtn.style.padding = "10px 15px 15px 10px";
    }
    lastScrollTop = currentScroll;
});
</script>
