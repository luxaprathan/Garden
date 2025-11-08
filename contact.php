<?php
include 'db.php';

$success = false; // Initialize success variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // SQL query to insert data into the database
    $sql = "INSERT INTO contact_form (first_name, phone_number, email, message)
            VALUES ('$first_name', '$phone_number', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {
        $success = true; // Set success to true on successful insertion
    } else {
        // Show error message if data insertion fails
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact | Garden</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/contact.css">
    <style>      
        #showcase {
            background-image: url('img/contact.jpg');
        }
        /* Success message styles */
        .success-message {
            background-color: #28a745;
            color: white;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <section id="showcase">
        <div class="text">
            <h2>Contact Us</h2>
        </div>
    </section>

    <section id="get-in-touch">
        <h2>Get In Touch</h2>
        <p>We are available 24/7 via e-mail. You can also use the quick contact form to ask questions about our services and projects.</p>
        
        <!-- Display success message if the form was submitted successfully -->
        <?php if ($success): ?>
            <div class="success-message">
                <h3>Your message has been sent successfully!</h3>
                <p>Thank you for contacting us. We will get back to you shortly.</p>
            </div>
        <?php endif; ?>

        <form id="contact-form" method="POST">
            <input type="text" name="first_name" placeholder="First name" required>
            <input type="tel" name="phone_number" placeholder="Phone number" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <textarea name="message" placeholder="Your message" required></textarea>
            <button class="call-to-action" type="submit">Send message</button>
        </form>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>
