<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'functions.php';
?>
<head>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Header Styles */
        header {
            width: 100%;
            /* background: linear-gradient(to right, #0a0a23, #001f3f); */
            /* background: linear-gradient(to right, #0077b6, #3498db); Blue gradient */
            background-color:#0a0a23;
            /* color: white; */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 8%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            top: 0;
            left: 0;
            position: absolute;
            z-index: 100;
        }

        header a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            font-family: 'Roboto', sans-serif;
            
        }
       a {
            list-style: none;
        }

        header #logo {
            font-size: 1.8em;
            font-weight: bold;
            font-family: 'Pacifico', cursive; /* Decorative font for the logo */
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        header nav li {
            display: inline-block;
        }

        header nav a {
            padding: 10px 15px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        header nav a:hover,
        header .active a {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            color: #000; /* Contrast for hover state */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px 5%;
            }

            header #logo {
                margin-bottom: 10px;
            }

            header nav ul {
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }

            header nav li {
                width: 100%;
            }

            header nav a {
                display: block;
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div id="logo">
                <?php if (isset($_SESSION["user_id"]) && !isAdmin()): ?>
                    <a href="home.php">My Garden</a>
                <?php elseif (isset($_SESSION["user_id"]) && isAdmin()): ?>
                    <a href="adminPanel.php">My Garden</a>
                <?php else: ?>
                    <a href="login.php">My Garden</a>
                <?php endif; ?>
            
        </div>
        <nav>
            <ul>
                <?php if (isset($_SESSION["user_id"]) && !isAdmin()): ?>
                    <li><a href="home.php">Home</a></li>
                <?php elseif (isset($_SESSION["user_id"]) && isAdmin()): ?>
                    <li><a href="adminPanel.php">Dashboard</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                
                <?php if (isset($_SESSION["user_id"])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
