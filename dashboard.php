<?php include 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/dashbored.css"> <!-- CSS File -->

</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar hidden" id="sidebar">
        <div class="user-info">
            <?php if (isUser()): ?>   
                <a href="userProfile.php">
                    <img src="<?php echo $_SESSION['img']; ?>" alt="User Image">
                </a>
                <a href="userProfile.php" id="name" class="<?php echo basename($_SERVER['PHP_SELF']) == 'user_profile.php' ? 'active' : ''; ?>">
                    <?php echo $_SESSION["user_name"]; ?>
                </a>
            <?php elseif (isAdmin()): ?>
                <a href="adminProfile.php">
                    <img src="<?php echo $_SESSION['img']; ?>" alt="Image">
                </a>
                <a href="adminProfile.php" id="name" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_profile.php' ? 'active' : ''; ?>">
                    <?php echo $_SESSION["user_name"]; ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- Navigation -->
        <nav class="navigation">
            <h3><i class='bx bxs-dashboard'></i> Dashboard</h3>
            <br>
            <div class="dashmenu">
                <?php if (isUser()): ?>
                    <a href="userMyCard.php"> My Card 🛒</a>
                    <a href="userMyOrders.php"> My Orders</a>
                <?php elseif (isAdmin()): ?>
                    <a href="adminAddProduct.php"class="<?php echo basename($_SERVER['PHP_SELF']) == 'ad_addProduct.php' ? 'active' : ''; ?>"> Add Products</a> 
                    <a href="adminManageUser.php"class="<?php echo basename($_SERVER['PHP_SELF']) == 'ad_manageUser.php' ? 'active' : ''; ?>"> Manage Users</a> 
                    <a href="adminViewFeedback.php"class="<?php echo basename($_SERVER['PHP_SELF']) == 'ad_Feedback.php' ? 'active' : ''; ?>"> View Feedback</a> 
                    <a href="adminViewIssues.php"class="<?php echo basename($_SERVER['PHP_SELF']) == 'ad_Issues.php' ? 'active' : ''; ?>"> View Issues</a> 
                    <a href="adminPanel.php"class="<?php echo basename($_SERVER['PHP_SELF']) == 'ad_order.php' ? 'active' : ''; ?>"> Manage Orders</a> 
                <?php endif; ?>
            </div>
        </nav>
        <img src="img/logo.png" class="footer_logo" alt="Logo" style="margin-left:35px;margin-top:10px;width: 200px; ">
    </aside>
    

    <!-- Menu Button (Hamburger Icon) -->
    <button id="menu-btn">
        <i class='bx bx-menu-alt-left'></i>
    </button>

   
    <!-- JavaScript File -->
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            var sidebar = document.getElementById("sidebar");
            var menuBtn = document.getElementById("menu-btn");
            var content = document.body; // Move entire body

           

            // Toggle sidebar visibility on button click
            menuBtn.addEventListener("click", function () {
                sidebar.classList.toggle("hidden");

                if (sidebar.classList.contains("hidden")) {
                    menuBtn.style.left = "0px";
                    content.style.marginLeft = "0"; // Move body back to normal
                    menuBtn.style.padding = "10px 15px 15px 10px";
                } else {
                    menuBtn.style.left = "250px";
                    menuBtn.style.padding = "0";
                    content.style.marginLeft = "298px"; // Move body to the right
                }

                // Toggle menu icon
                var menuIcon = menuBtn.querySelector("i");
                menuIcon.classList.toggle("bx-menu-alt-left");
                menuIcon.classList.toggle("bx-menu-alt-right");
            });
        });
        var lastScrollTop = 0;
        window.addEventListener("scroll", function () {
            var sidebar = document.getElementById("sidebar");
            var menuBtn = document.getElementById("menu-btn");
            var currentScroll = window.scrollY;
            var content = document.body;

            if (currentScroll > lastScrollTop) {
                sidebar.classList.add("hidden");
                content.style.marginLeft = "0";
                menuBtn.style.left = "0px";  
                menuBtn.style.padding = "10px 15px 15px 10px";
            }
            lastScrollTop = currentScroll;
        });

    </script>
</body>
</html>
