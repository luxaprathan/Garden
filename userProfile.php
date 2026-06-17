<?php
session_start();
include_once "db.php";
include_once "functions.php";

requireCustomer();

$ID = $_SESSION['user_id'];
$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $nic = $_POST['nic'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    $nicError = validateNic($nic);
    $ageError = validateAgeFromDob($dob);
    if ($nicError || $ageError) {
        $message = $nicError ?: $ageError;
        $messageType = 'error';
    } else {
        $updateQuery = "UPDATE users SET first_name=?, last_name=?, NIC=?, Email=?, DOB=?, Gender=?, Address=? WHERE ID=?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "sssssssi", $firstName, $lastName, $nic, $email, $dob, $gender, $address, $ID);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Profile updated successfully.";
            $messageType = 'success';
            $_SESSION["user_name"] = $firstName . " " . $lastName;
            $_SESSION["email"] = $email;
        } else {
            $message = "Error updating profile.";
            $messageType = 'error';
        }
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePassword'])) {
    $pwdError = validatePassword($_POST['newPassword']);
    if ($pwdError) {
        $message = $pwdError;
        $messageType = 'error';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE ID = ?");
        mysqli_stmt_bind_param($stmt, "i", $ID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $storedPassword);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if (password_verify($_POST['currentPassword'], $storedPassword)) {
            $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
            $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "si", $newPassword, $ID);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Password updated successfully.";
                $messageType = 'success';
            } else {
                $message = "Error updating password.";
                $messageType = 'error';
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Incorrect current password.";
            $messageType = 'error';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changeProfile']) && !empty($_FILES['image']['name'])) {
    $target_file = "profiles/" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    $stmt = mysqli_prepare($conn, "UPDATE users SET img = ? WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "si", $target_file, $ID);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Profile photo updated successfully.";
        $messageType = 'success';
        $_SESSION["img"] = $target_file;
    } else {
        $message = "Error uploading image.";
        $messageType = 'error';
    }
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE ID = ?");
mysqli_stmt_bind_param($stmt, "i", $ID);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

ensureMessagingSchema($conn);
$userEmail = $user['email'];

$feedbackStmt = mysqli_prepare($conn, "SELECT * FROM contact_form WHERE email = ? OR user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($feedbackStmt, "si", $userEmail, $ID);
mysqli_stmt_execute($feedbackStmt);
$userFeedback = mysqli_stmt_get_result($feedbackStmt);
mysqli_stmt_close($feedbackStmt);

$issuesStmt = mysqli_prepare($conn, "SELECT * FROM issues WHERE email = ? OR user_id = ? ORDER BY reported_at DESC");
mysqli_stmt_bind_param($issuesStmt, "si", $userEmail, $ID);
mysqli_stmt_execute($issuesStmt);
$userIssues = mysqli_stmt_get_result($issuesStmt);
mysqli_stmt_close($issuesStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/dashbored.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user-profile.css">
</head>
<body class="user-profile-page">

<?php include_once "navbar.php"; ?>

<div class="user-profile-wrap">
    <h1 class="page-title">My Profile</h1>

    <?php if ($message): ?>
        <div class="alert-banner <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="user-header">
        <img src="<?= htmlspecialchars($user['img'] ?? 'profiles/User.jpg') ?>" alt="Profile">
        <div class="user-header-info">
            <span class="user-badge">Customer</span>
            <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <?php if (!empty($user['phone']) || !empty($user['Address'])): ?>
                <p class="user-meta">
                    <?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : '' ?>
                    <?= !empty($user['phone']) && !empty($user['Address']) ? ' · ' : '' ?>
                    <?= !empty($user['Address']) ? htmlspecialchars($user['Address']) : '' ?>
                </p>
            <?php endif; ?>
        </div>
        <a href="home.php" class="home-link">← Home</a>
    </div>

    <div class="user-card">
        <h3>Personal Information</h3>
        <form method="POST" data-validate>
            <div class="user-form-grid">
                <div class="user-field">
                    <label>First Name</label>
                    <input type="text" name="firstName" value="<?= htmlspecialchars($user['first_name']) ?>" class="editable" readonly>
                </div>
                <div class="user-field">
                    <label>Last Name</label>
                    <input type="text" name="lastName" value="<?= htmlspecialchars($user['last_name']) ?>" class="editable" readonly>
                </div>
                <div class="user-field">
                    <label>NIC</label>
                    <input type="text" name="nic" value="<?= htmlspecialchars($user['NIC'] ?? '') ?>" class="editable" data-rule="nic" readonly>
                </div>
                <div class="user-field">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="editable" readonly>
                </div>
                <div class="user-field">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($user['DOB'] ?? '') ?>" class="editable" data-rule="ageFromDob" readonly>
                </div>
                <div class="user-field">
                    <label>Gender</label>
                    <input type="text" name="gender" value="<?= htmlspecialchars($user['Gender'] ?? '') ?>" class="editable" readonly>
                </div>
                <div class="user-field full">
                    <label>Address</label>
                    <textarea name="address" class="editable" readonly><?= htmlspecialchars($user['Address'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="user-actions">
                <button type="button" id="edit-btn" class="ubtn ubtn-blue" onclick="enableEdit()">Edit Profile</button>
                <button type="submit" name="updateProfile" id="update-btn" class="ubtn ubtn-green" style="display:none;">Save Changes</button>
                <button type="button" id="cancel-btn" class="ubtn ubtn-gray" style="display:none;" onclick="cancelEdit()">Cancel</button>
                <button type="button" id="change-password-btn" class="ubtn ubtn-outline">Change Password</button>
                <button type="button" id="change-photo-btn" class="ubtn ubtn-gray">Change Photo</button>
            </div>
        </form>
    </div>

    <h3 class="messages-title">My Messages</h3>
    <div class="messages-grid">
        <div class="msg-panel">
            <h4>My Feedback</h4>
            <?php if ($userFeedback && mysqli_num_rows($userFeedback) > 0): ?>
                <?php while ($fb = mysqli_fetch_assoc($userFeedback)): ?>
                    <div class="msg-item">
                        <div class="msg-item-top">
                            <span class="msg-date"><?= htmlspecialchars($fb['created_at']) ?></span>
                        </div>
                        <p class="msg-body"><?= htmlspecialchars($fb['message']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-msg">No feedback yet. <a href="contact.php">Send feedback</a></p>
            <?php endif; ?>
        </div>

        <div class="msg-panel issues">
            <h4>My Issues</h4>
            <?php if ($userIssues && mysqli_num_rows($userIssues) > 0): ?>
                <?php while ($issue = mysqli_fetch_assoc($userIssues)): ?>
                    <?php
                        $status = $issue['status'] ?? 'pending';
                        $statusClass = issueStatusClass($status);
                        $statusLabel = issueStatusLabel($status);
                    ?>
                    <div class="msg-item issue">
                        <div class="msg-item-top">
                            <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            <span class="msg-date"><?= htmlspecialchars($issue['reported_at']) ?></span>
                        </div>
                        <p class="msg-body"><?= htmlspecialchars($issue['issue']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-msg">No issues reported. <a href="contact.php">Report an issue</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="passwordModal" class="user-modal">
    <div class="user-modal-box">
        <button class="modal-x" type="button" onclick="closeModal('passwordModal')">&times;</button>
        <h3>Change Password</h3>
        <form method="POST" data-validate>
            <div class="user-field">
                <label>Current Password</label>
                <input type="password" name="currentPassword" required>
            </div>
            <div class="user-field">
                <label>New Password</label>
                <input type="password" name="newPassword" data-rule="password" required>
            </div>
            <button type="submit" name="changePassword" class="ubtn ubtn-blue">Update Password</button>
        </form>
    </div>
</div>

<div id="photoModal" class="user-modal">
    <div class="user-modal-box">
        <button class="modal-x" type="button" onclick="closeModal('photoModal')">&times;</button>
        <h3>Change Profile Photo</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="user-field">
                <label>Select Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="changeProfile" class="ubtn ubtn-blue">Upload Photo</button>
        </form>
    </div>
</div>

<?php include_once "dashboard.php"; ?>

<script src="js/validation.js"></script>
<script>
    function enableEdit() {
        document.querySelectorAll('.editable').forEach(function (f) { f.removeAttribute('readonly'); });
        document.getElementById('update-btn').style.display = 'inline-block';
        document.getElementById('cancel-btn').style.display = 'inline-block';
        document.getElementById('edit-btn').style.display = 'none';
    }
    function cancelEdit() { location.reload(); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.getElementById('change-password-btn').onclick = function () {
        document.getElementById('passwordModal').classList.add('open');
    };
    document.getElementById('change-photo-btn').onclick = function () {
        document.getElementById('photoModal').classList.add('open');
    };
</script>
</body>
</html>
