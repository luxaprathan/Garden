<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | My Garden</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/pages.css">
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section class="page-hero" style="background-image: url('img/home.jpg');">
        <div class="page-hero-overlay">
            <h1>About Us</h1>
            <p>Your trusted partner for quality garden products</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container">
            <div class="info-grid">
                <div class="info-card">
                    <h2>Who We Are</h2>
                    <p>My Garden is an online store dedicated to helping you grow beautiful outdoor spaces. We offer hand-picked plants, tools, and garden essentials for beginners and experienced gardeners alike.</p>
                </div>
                <div class="info-card">
                    <h2>What We Do</h2>
                    <p>We source quality products at fair prices and deliver them straight to your door. Browse our collection, add items to your cart, and track your orders easily from your account.</p>
                </div>
                <div class="info-card">
                    <h2>Why Choose Us</h2>
                    <ul class="simple-list">
                        <li>Curated garden products</li>
                        <li>Secure online ordering</li>
                        <li>Friendly customer support</li>
                        <li>Regular sales and new arrivals</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="page-section alt-bg">
        <div class="page-container narrow">
            <h2 class="section-heading">Our Story</h2>
            <p class="section-text">Started in 2020, My Garden began as a small local shop with a passion for greenery. Today we serve customers online with the same care — helping every home and backyard flourish one plant at a time.</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container narrow text-center">
            <h2 class="section-heading">Get in Touch</h2>
            <p class="section-text">Have a question or want to share feedback? We'd love to hear from you.</p>
            <a href="contact.php" class="page-btn">Contact Us</a>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>

</html>