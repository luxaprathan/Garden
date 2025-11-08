<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Validate passwords
    if ($password !== $confirm_password) {
        $message = "<p class='error'>Passwords do not match!</p>";
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "<p class='error'>This email is already registered. Please use another email.</p>";
    }
    $check_stmt->close();

    // Hash the password for security
    if (empty($message)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $sql = "INSERT INTO users (first_name, last_name, email, password,img) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $img="profiles/User.jpg";
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password,$img);

        if ($stmt->execute()) {
            $_SESSION["message"] = "<p class='success'>Registration successful! Redirecting to Dashboard...</p>";
            $_SESSION["first_name"] = $first_name;
            $_SESSION["last_name"] = $last_name;
            $_SESSION["user_name"] = $first_name . " " . $last_name;

                    $email = $email;
                    $sql = "SELECT id FROM users WHERE email = ?";
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt) {
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->store_result();
                
                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($id);
                            $stmt->fetch();
                
                            // Verify hashed password
                            if (isset($id)) {
                                $_SESSION["user_id"] = $id;
                                header("Location: home.php");
                                exit; 
                            } else {
                                $message = "<p class='error'> Please try again.</p>";
                            }
                        }
                    }
        } else {
            $message = "<p class='error'>Could not register. Please try again later.</p>";
        }

        $stmt->close();
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<video class="video-bg" autoplay muted loop>
    <source src="img/nlock.mov" type="video/mp4">
    Your browser does not support HTML5 video.
</video>

<div class="container">
    <div class="form-container">
        <div class="signup active" id="signup">
            <h2>Sign Up</h2>  
            <?php 
                if (isset($_SESSION["message"])) {
                    echo $_SESSION["message"];
                    unset($_SESSION["message"]);
                } 
                echo $message; 
            ?>
        
            <form method="POST">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="signup">SIGN UP</button>
                <p>Back to <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
</div>

</body>
</html>
