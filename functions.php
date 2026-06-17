<?php
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['user_id']) && (
        $_SESSION['user_id'] === 1 ||
        (isset($_SESSION['email']) && $_SESSION['email'] === 'admin@gmail.com')
    );
}

function isUser()
{
    return isLoggedIn() && !isAdmin();
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: home.php');
        exit;
    }
}

function requireCustomer()
{
    requireLogin();
    if (isAdmin()) {
        header('Location: adminPanel.php');
        exit;
    }
}

function validatePassword($password)
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters with uppercase and lowercase letters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }
    return null;
}

function validatePhone($phone)
{
    $phone = preg_replace('/\D/', '', $phone);
    if (!preg_match('/^\d{10}$/', $phone)) {
        return 'Phone number must be exactly 10 digits.';
    }
    return null;
}

function validateEmail($email)
{
    if (!preg_match('/^[^\s@]+@[^\s@]+\.com$/i', $email)) {
        return 'Email must include @ and end with .com.';
    }
    return null;
}

function validateNic($nic)
{
    if (empty($nic)) {
        return null;
    }
    if (!preg_match('/^(\d{12}|\d{9}[A-Za-z])$/', $nic)) {
        return 'NIC must be 12 digits or 9 digits followed by one letter.';
    }
    return null;
}

function validateAgeFromDob($dob)
{
    if (empty($dob)) {
        return null;
    }
    $birth = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birth)->y;
    if ($age < 15) {
        return 'Age cannot be less than 15 years.';
    }
    return null;
}

function validatePrice($price)
{
    if (!is_numeric($price) || floatval($price) < 0) {
        return 'Price must be a valid number.';
    }
    return null;
}

function validateQuantity($quantity)
{
    if (!is_numeric($quantity) || intval($quantity) < 0) {
        return 'Quantity cannot be less than 0.';
    }
    return null;
}

function ensureMessagingSchema($conn)
{
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM issues");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }
    if (!in_array('status', $columns)) {
        $conn->query("ALTER TABLE issues ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }
    if (!in_array('user_id', $columns)) {
        $conn->query("ALTER TABLE issues ADD COLUMN user_id INT NULL");
    }

    $feedbackCols = [];
    $result = $conn->query("SHOW COLUMNS FROM contact_form");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $feedbackCols[] = $row['Field'];
        }
    }
    if (!in_array('user_id', $feedbackCols)) {
        $conn->query("ALTER TABLE contact_form ADD COLUMN user_id INT NULL");
    }
}

function issueStatusLabel($status)
{
    $labels = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
    ];
    return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

function issueStatusClass($status)
{
    $classes = [
        'pending' => 'status-pending',
        'in_progress' => 'status-in-progress',
        'resolved' => 'status-resolved',
        'rejected' => 'status-rejected',
    ];
    return $classes[$status] ?? 'status-pending';
}
