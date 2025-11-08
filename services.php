<?php
// Include database connection
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $issue = $_POST['issue'];

    // Insert the issue into the database
    $sql = "INSERT INTO issues (name, email, issue) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $issue);
    
    if ($stmt->execute()) {
        $successMessage = "Your issue has been reported successfully!";
    } else {
        $errorMessage = "There was an error reporting the issue. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Services | Garden</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/services.css">
    <style>
        #showcase {
            background-image: url('img/services.jpg');
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="text">
    <br><br><center><h2>GARDENING TOOLS & SERVICES</h2></center><br><br>
</div>

<section id="video-section">
    <video autoplay loop muted playsinline>
        <source src="img/services.mov" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</section>

<section id="issue-form">
    <h2>Report an Issue</h2>
    <?php
    if (isset($successMessage)) {
        echo "<p style='color: green;'>$successMessage</p>";
    }
    if (isset($errorMessage)) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
    ?>
    <form action="services.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="issue">Issue:</label>
        <textarea id="issue" name="issue" rows="4" required></textarea>
        
        <button type="submit">Submit</button>
    </form>
</section>

<section id="showcase">
        <div class="text">
            <h2>Services</h2>
        </div>
</section>

<section id="services">
    <h2>Explore Our Gardening Tool Services!</h2>
    <div class="services-container">
        <div class="service service-one">
            <h3>Garden Tool Sales</h3>
            <p>We provide high-quality gardening tools, including pruners, shovels, gloves, and more to help you maintain a beautiful garden.</p>
        </div>
        <div class="service service-two">
            <h3>Custom Garden Solutions</h3>
            <p>Need help choosing the right tools for your garden? Our experts offer personalized consultations to guide you in selecting the best products.</p>
        </div>
        <div class="service service-three">
            <h3>Maintenance & Repair</h3>
            <p>We offer maintenance and repair services for your gardening tools to ensure they last longer and perform at their best.</p>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
