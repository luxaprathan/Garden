<?php 
session_start();
include 'db.php';

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST"&& isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check user in the database
    $sql = "SELECT id, first_name, last_name, password,img FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $first_name, $last_name, $hashed_password,$img);
            $stmt->fetch();

            // Verify hashed password
            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["user_name"] = $first_name . " " . $last_name;
                $_SESSION["email"] = $email;
                $_SESSION["img"] = $img;

                // Set success message
                $_SESSION["message"] = "<p class='success'>Login successful! Redirecting...</p>";
                header("Location: home.php");
                // header("refresh:2; url=dashboard.php");
                exit;
            } else {
                $message = "<p class='error'>Incorrect password! Please try again.</p>";
            }
        } else {
            $message = "<p class='error'>User not found! Please check your email.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p class='error'>Database error! Please try again later.</p>";
    }
    $conn->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
    <body style="background-image: url('img/home.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">

</head>
<body>



<div class="container">
    <div class="form-container">
        <div class="login active" id="login">
            <h2>Login</h2> 
            <?php 
                if (isset($_SESSION["message"])) {
                    echo $_SESSION["message"];
                    unset($_SESSION["message"]);
                } 
                echo $message; 
            ?>
            <form method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Do not have an account? <a href="userRegister.php" >Create account</a></p>
            </form>
        </div>
        
    </div>
</div>

</body>
</html>
