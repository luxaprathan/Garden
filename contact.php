<?php
session_start();
include 'db.php';
include 'functions.php';

ensureMessagingSchema($conn);

$success = false;
$error = "";
$successType = "";

$defaultName = '';
$defaultEmail = '';
if (isset($_SESSION['user_name'])) {
    $defaultName = $_SESSION['user_name'];
}
if (isset($_SESSION['email'])) {
    $defaultEmail = $_SESSION['email'];
}
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message_type = $_POST['message_type'] ?? 'feedback';
    $first_name = trim($_POST['first_name']);
    $phone_number = preg_replace('/\D/', '', $_POST['phone_number']);
    $email = trim($_POST['email']);
    $message_text = trim($_POST['message']);

    $phoneError = validatePhone($phone_number);
    $emailError = validateEmail($email);
    if (!in_array($message_type, ['feedback', 'issue'])) {
        $error = "Please select a valid message type.";
    } elseif (empty($first_name) || empty($email) || empty($message_text)) {
        $error = "All fields are required.";
    } elseif ($emailError) {
        $error = $emailError;
    } elseif ($phoneError) {
        $error = $phoneError;
    } elseif ($message_type === 'feedback') {
        if ($userId) {
            $sql = "INSERT INTO contact_form (first_name, phone_number, email, message, user_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $first_name, $phone_number, $email, $message_text, $userId);
        } else {
            $sql = "INSERT INTO contact_form (first_name, phone_number, email, message) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $first_name, $phone_number, $email, $message_text);
        }

        if ($stmt->execute()) {
            $success = true;
            $successType = 'feedback';
        } else {
            $error = "Failed to send feedback. Please try again.";
        }
        $stmt->close();
    } else {
        $status = 'pending';
        if ($userId) {
            $sql = "INSERT INTO issues (name, email, issue, status, user_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $first_name, $email, $message_text, $status, $userId);
        } else {
            $sql = "INSERT INTO issues (name, email, issue, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $first_name, $email, $message_text, $status);
        }

        if ($stmt->execute()) {
            $success = true;
            $successType = 'issue';
        } else {
            $error = "Failed to report issue. Please try again.";
        }
        $stmt->close();
    }

    if ($success) {
        $defaultName = $defaultName ?: '';
        $defaultEmail = $defaultEmail ?: '';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | My Garden</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/pages.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <section class="page-hero" style="background-image: url('img/contact.jpg');">
        <div class="page-hero-overlay">
            <h1>Contact Us</h1>
            <p>Send feedback or report an issue — our team will respond soon</p>
        </div>
    </section>

    <section class="page-section">
        <div class="page-container contact-layout">
            <div class="contact-details">
                <h2>Reach Us</h2>
                <div class="detail-item">
                    <strong>Email</strong>
                    <p>support@mygarden.com</p>
                </div>
                <div class="detail-item">
                    <strong>Phone</strong>
                    <p>+94 11 234 5678</p>
                </div>
                <div class="detail-item">
                    <strong>Hours</strong>
                    <p>Mon – Sat: 9:00 AM – 6:00 PM</p>
                </div>
                <div class="detail-item">
                    <strong>Address</strong>
                    <p>123 Garden Street, Colombo, Sri Lanka</p>
                </div>
                <p class="contact-note">
                    Choose <strong>Feedback</strong> for questions or suggestions (admin → View Feedback).<br>
                    Choose <strong>Issue</strong> to report a problem (admin → View Issues). Track issue status on your profile page.
                </p>
            </div>

            <div class="contact-form-wrap">
                <h2 id="formTitle">Send Message</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php if ($successType === 'issue'): ?>
                            Your issue has been reported. Track its status on your <a href="userProfile.php">profile page</a>.
                        <?php else: ?>
                            Thank you! Your feedback has been sent to our admin team.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" data-validate class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label for="message_type">Type</label>
                        <select name="message_type" id="message_type" required onchange="updateFormLabels()">
                            <option value="feedback" <?= (isset($_POST['message_type']) && $_POST['message_type'] === 'feedback') || !isset($_POST['message_type']) ? 'selected' : '' ?>>Feedback</option>
                            <option value="issue" <?= isset($_POST['message_type']) && $_POST['message_type'] === 'issue' ? 'selected' : '' ?>>Issue</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Your Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Full name" value="<?= htmlspecialchars($defaultName) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" placeholder="10-digit phone number" data-rule="phone" required
                            value="<?= isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" data-rule="email" value="<?= htmlspecialchars($defaultEmail) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="message" id="messageLabel">Your Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your feedback here..." required><?= $success ? '' : (isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '') ?></textarea>
                    </div>
                    <button type="submit" class="page-btn full-width" id="submitBtn">Send Feedback</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <script src="js/validation.js"></script>
    <script>
        function updateFormLabels() {
            var type = document.getElementById('message_type').value;
            var messageLabel = document.getElementById('messageLabel');
            var messageField = document.getElementById('message');
            var submitBtn = document.getElementById('submitBtn');
            var formTitle = document.getElementById('formTitle');

            if (type === 'issue') {
                formTitle.textContent = 'Report an Issue';
                messageLabel.textContent = 'Describe the Issue';
                messageField.placeholder = 'Describe the problem you encountered...';
                submitBtn.textContent = 'Report Issue';
            } else {
                formTitle.textContent = 'Send Feedback';
                messageLabel.textContent = 'Your Feedback';
                messageField.placeholder = 'Write your feedback, question, or suggestion here...';
                submitBtn.textContent = 'Send Feedback';
            }
        }
        document.addEventListener('DOMContentLoaded', updateFormLabels);
    </script>
</body>

</html>