<?php 
session_start();
include 'db.php';
include 'functions.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $pwdError = validatePassword($password);
    if ($pwdError) {
        $message = "<div class='msg error'>" . htmlspecialchars($pwdError) . "</div>";
    } else {
        $sql = "SELECT id, first_name, last_name, password, img FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $first_name, $last_name, $hashed_password, $img);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION["user_id"] = $id;
                    $_SESSION["user_name"] = $first_name . " " . $last_name;
                    $_SESSION["email"] = $email;
                    $_SESSION["img"] = $img;
                    $_SESSION["message"] = "<div class='msg success'>Login successful! Redirecting...</div>";

                    if ($email === 'admin@gmail.com' || $id === 1) {
                        header("Location: adminPanel.php");
                    } else {
                        header("Location: home.php");
                    }
                    exit;
                } else {
                    $message = "<div class='msg error'>Incorrect password! Please try again.</div>";
                }
            } else {
                $message = "<div class='msg error'>User not found! Please check your email.</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='msg error'>Database error! Please try again later.</div>";
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Garden Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            min-height: 100vh;
        }

        .page {
            display: flex;
            min-height: 100vh;
        }

        .left {
            flex: 1;
            background: url('img/home.jpg') center center / cover no-repeat;
            min-height: 100vh;
        }

        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 40px 30px;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
        }

        .form-box h2 {
            font-size: 28px;
            color: #0a0a23;
            margin-bottom: 6px;
        }

        .form-box .desc {
            color: #666;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .msg {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .msg.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .msg.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        form label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        form input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 16px;
            background: #fafafa;
        }

        form input:focus {
            outline: none;
            border-color: #27ae60;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.15);
        }

        form input.input-error {
            border-color: #dc3545;
        }

        .field-error {
            color: #dc3545;
            font-size: 12px;
            margin: -12px 0 12px;
            display: block;
        }

        .hint {
            background: #f4f9f6;
            border: 1px solid #d5ece0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #555;
            margin-bottom: 16px;
            list-style: none;
            line-height: 1.7;
        }

        .hint li::before {
            content: "• ";
            color: #27ae60;
        }

        button[type="submit"] {
            width: 100%;
            padding: 13px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background: #219a52;
        }

        .link-text {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .link-text a {
            color: #27ae60;
            font-weight: 600;
            text-decoration: none;
        }

        .link-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page { flex-direction: column; }
            .left { min-height: 240px; flex: none; }
            .right { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="left"></div>

    <div class="right">
        <div class="form-box">
            <?php
                if (isset($_SESSION["message"])) {
                    echo $_SESSION["message"];
                    unset($_SESSION["message"]);
                }
                echo $message;
            ?>

            <h2>Login</h2>
            <p class="desc">Sign in to your account</p>

            <form method="post" data-validate>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" data-rule="password" required>

                <ul class="hint">
                    <li>At least 8 characters</li>
                    <li>One uppercase letter (A–Z)</li>
                    <li>One lowercase letter (a–z)</li>
                </ul>

                <button type="submit" name="login">Login</button>
            </form>

            <p class="link-text">
                Don't have an account? <a href="userRegister.php">Create account</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.getElementById('toLogin').addEventListener('click', () => {
    document.getElementById('login').classList.add('active');
    document.getElementById('signup').classList.remove('active');
});

document.getElementById('toSignUp').addEventListener('click', () => {
    document.getElementById('signup').classList.add('active');
    document.getElementById('login').classList.remove('active');
});

</script>
</body>
</html>
