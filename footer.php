<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- <link rel="stylesheet" href="css/footer.css">  -->
    <!-- Link to the enhanced CSS file -->
     <style>
        /* Footer Styles */
    footer {
    width: 100%;
    margin-top:25px;
    /* background: linear-gradient(to right, #0a0a23, #001f3f);  */
    background-color:#0a0a23;
    color: #ffffff;
    padding: 50px 0 30px;
    font-size: 14px;
    line-height: 22px;
}

.row {
    width: 85%;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
}

.col {
    flex-basis: 25%;
    padding: 10px;
}

.col:nth-child(2), .col:nth-child(3) {
    flex-basis: 15%;
}

.footer_logo {
    width: 100px;
    position: relative;
    top: -20px;
    left: -10px;
}

.footer_about {
    position: relative;
    top: -20px;
}

.col h3 {
    width: fit-content;
    margin-bottom: 30px;
    position: relative;
    font-size: 18px;
    font-weight: bold;
    color: #f2f2f2; /* Lighter text for contrast */
}

.footer_email {
    width: fit-content;
    border-bottom: 1px solid #f39c12; /* Highlight color */
    margin: 20px 0;
    color: #f1c40f;
}

.col ul li {
    list-style: none;
    margin-bottom: 12px;
}

.col ul li a {
    text-decoration: none;
    color: #ffffff;
    transition: color 0.3s ease;
}

.col ul li a:hover {
    color: #f39c12; /* Hover effect */
}

.col form {
    padding-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f39c12;
    margin-bottom: 50px;
}

.col form .icon {
    font-size: 18px;
    margin-right: 10px;
}

.col form input {
    width: 100%;
    background: transparent;
    color: #ccc;
    border: 0;
    outline: none;
    font-size: 14px;
}

.col form button {
    background: transparent;
    border: 0;
    outline: none;
    cursor: pointer;
}

.col form button .icon_right {
    font-size: 16px;
    color: #ccc;
}

.col .social_icons .social_icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    text-align: center;
    line-height: 30px;
    font-size: 20px;
    color: #ffffff;
    background: #f39c12;
    margin-right: 15px;
    padding: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.col .social_icons .social_icon:hover {
    background: #e67e22; /* Slightly darker hover effect */
}

hr {
    width: 90%;
    border: 0;
    border-bottom: 1px solid #34495e;
    margin: 20px auto;
}

footer .copyright {
    text-align: center;
    color: #bdc3c7;
}

.bottom_line {
    width: 100%;
    height: 5px;
    background: #2c3e50;
    border-radius: 3px;
    position: absolute;
    top: 25px;
    left: 0;
    overflow: hidden;
}

.bottom_line span {
    width: 15px;
    height: 100%;
    background: #f39c12;
    border-radius: 3px;
    position: absolute;
    top: 0;
    left: 10px;
    animation: moveline 2s linear infinite;
}

@keyframes moveline {
    0% {
        left: -20px;
    }
    100% {
        left: 100%;
    }
}


     </style>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <footer>
        <div class="row">
            <!-- Logo and About Section -->
            <div class="col">
                <img src="img/logo.png" class="footer_logo" alt="Logo" style="margin-left:100px; ">
                <p class="footer_about">
                    <?php 
                        echo "Welcome to our garden site. Explore the beauty of nature and find 
                              everything you need to make your garden thrive.";
                    ?>
                </p>
            </div>

            <!-- Contact Information -->
            <div class="col">
                <h3>Office <div class="bottom_line"><span></span></div></h3>
                <p><?php echo "Jaffna Kantharmadam"; ?></p>
                <p><?php echo "Jaffna, Northern Province"; ?></p>
                <p><?php echo "Northern University Campus"; ?></p>
                <p class="footer_email"><?php echo "Garden@gmail.com"; ?></p>
                <h4><?php echo "+94 0775880924"; ?></h4>
            </div>

            <!-- Quick Links -->
            <div class="col">
                <h3>Links <div class="bottom_line"><span></span></div></h3>
                <ul>
                    <?php
                        $links = [
                            "HOME" => "home.php",
                            "ABOUT US" => "about.php",
                            "CONTACT" => "contact.php",
                            "PRODUCT" => "#"
                        ];

                        foreach ($links as $text => $url) {
                            echo "<li><a href='$url'>$text</a></li>";
                        }
                    ?>
                </ul>
            </div>

            <!-- Newsletter Subscription -->
            <div class="col">
                <h3>Newsletter <div class="bottom_line"><span></span></div></h3>
                <form action="subscribe.php" method="POST">
                    <ion-icon class="icon" name="mail"></ion-icon>
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit"><ion-icon class="icon_right" name="arrow-round-forward"></ion-icon></button>
                </form>
                <div class="social_icons">
                    <a href="https://facebook.com" target="_blank">
                        <ion-icon class="social_icon" name="logo-facebook"></ion-icon>
                    </a>
                    <a href="https://whatsapp.com" target="_blank">
                        <ion-icon class="social_icon" name="logo-whatsapp"></ion-icon>
                    </a>
                    <a href="https://twitter.com" target="_blank">
                        <ion-icon class="social_icon" name="logo-twitter"></ion-icon>
                    </a>
                    <a href="https://instagram.com" target="_blank">
                        <ion-icon class="social_icon" name="logo-instagram"></ion-icon>
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <p class="copyright">
            <?php 
                echo "Cosas Learning Ⓒ " . date("Y") . " - All Rights Reserved"; 
            ?>
        </p>
    </footer>
</body>
</html>
