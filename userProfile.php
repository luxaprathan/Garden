<?php 
// session_start();
include_once "navbar.php"; 
include_once "db.php"; 
if (!isset($_SESSION['user_id']) ) {
    header("Location: login.php");
    exit;
}

$ID = $_SESSION['user_id'];
$message = "";

// Handle Profile Update (Using Prepared Statements)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $nic = $_POST['nic'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    $updateQuery = "UPDATE users SET 
        first_name = ?, 
        last_name = ?, 
        NIC = ?, 
        Email = ?, 
        DOB = ?, 
        Gender = ?, 
        Address = ? 
        WHERE ID = ?";
    
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssssssi", $firstName, $lastName, $nic, $email, $dob, $gender, $address, $ID);
    
    if (mysqli_stmt_execute($stmt)) {
        $message = "<p class='success'>Profile updated successfully.</p>";
        $_SESSION["user_name"] = $firstName . " " . $lastName;
    } else {
        $message = "<p class='error'>Error updating profile</p>";
    }
    mysqli_stmt_close($stmt);
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);

    $query = "SELECT password FROM users WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $ID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $storedPassword);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (password_verify($currentPassword, $storedPassword)) {
        $updatePasswordQuery = "UPDATE users SET password = ? WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $updatePasswordQuery);
        mysqli_stmt_bind_param($stmt, "si", $newPassword, $ID);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<p class='success'>Password updated successfully!.</p>";
        } else {
            $message ="<p class='error'>Error updating password</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<p class='error'>Incorrect current password!.</p>";
    }
}

// Handle Profile Change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changeProfile']) && isset($_FILES['image'])) {
    $image = $_FILES['image']['name'];
    $target_dir = "profiles/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    $updateImageQuery = "UPDATE users SET img = ? WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $updateImageQuery);
    mysqli_stmt_bind_param($stmt, "si",$target_file, $ID);

    if (mysqli_stmt_execute($stmt)) {
        $message = "<p class='success'>Profile image updated successfully!.</p>";
        $_SESSION["img"] = $target_file;
    } else {
        $message = "<p class='error'>Error uploading image. Please try again.</p>";
    }
    mysqli_stmt_close($stmt);

}

// Fetch user Profile Data
$query = "SELECT * FROM users WHERE ID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<title>profile</title>
<link rel="stylesheet" href="css/profile.css">

<h2>Admin Profile</h2>
    <div id="messageModal" class="meg" style="display: <?php echo ($message ? 'block' : 'none'); ?>;">
    <p><?php echo $message; ?></p>
    </div>
<form class="profile-form" method="POST">

    <div>
        <img src="<?php echo htmlspecialchars($admin['img'] ?? 'img/User.jpg'); ?>" alt="Profile Picture" class="img"> <br> <br>
    </div>

    <div>
        <label>First Name</label>
        <input type="text" name="firstName" value="<?php echo htmlspecialchars($admin['first_name']); ?>" class="editable" readonly><br>

        <label>Last Name</label>
        <input type="text" name="lastName" value="<?php echo htmlspecialchars($admin['last_name']); ?>" class="editable" readonly><br>

        <label>NIC</label>
        <input type="text" name="nic" value="<?php echo htmlspecialchars($admin['NIC']); ?>" class="editable" readonly><br>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" class="editable" readonly><br>

        <label>DOB</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($admin['DOB']); ?>" class="editable" readonly><br>

        <label>Gender</label>
        <input type="text" name="gender" value="<?php echo htmlspecialchars($admin['Gender']); ?>" class="editable" readonly><br>

        <label>Address</label><br>
        <textarea name="address" class="editable" readonly><?php echo htmlspecialchars($admin['Address']); ?></textarea><br>

        <button type="button" id="edit-btn" onclick="enableEdit()">Edit</button>
        <button type="submit" name="updateProfile" id="update-btn" style="display:none;">Update</button>
        <button type="submit" name="backProfile" id="back-btn" style="display:none;">Back</button>
        <button type="button" id="change-password-btn">Change Password</button>
        <button type="button" id="change-profile-btn">Change Profile</button>
    </div>
</form>


<!-- Password Change Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Change Password</h3>

        <form method="POST">
            <label>Current Password</label>
            <input type="password" name="currentPassword" required><br>
            <label>New Password</label>
            <input type="password" name="newPassword" required>
            <button type="submit" name="changePassword">Update Password</button>
        </form>
    </div>
</div>

<!-- Profile Change Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Change Profile</h3>
        <form method="POST"enctype="multipart/form-data">
        <label>Image:</label>
        <input type="file" name="image" required><br><br>
            <button type="submit" name="changeProfile">Save</button>
        </form>
    </div>
</div>
<?php include_once "footer.php"; ?>
<?php include_once "dashboard.php"; ?>

<script>
    function enableEdit() {
        document.querySelectorAll('.editable').forEach(field => field.removeAttribute('readonly'));
        document.getElementById('update-btn').style.display = 'inline-block';
        document.getElementById('back-btn').style.display = 'inline-block';
        document.getElementById('change-password-btn').style.display = 'none';
        document.getElementById('edit-btn').style.display = 'none';
    }

    document.getElementById("change-password-btn").onclick = function() {
        document.getElementById("passwordModal").style.display = "block";
    };

    document.getElementById("change-profile-btn").onclick = function() {
        document.getElementById("profileModal").style.display = "block";
    };

    function closeModal() {
        document.getElementById("passwordModal").style.display = "none";
        document.getElementById("profileModal").style.display = "none";
    }

    function closeMessageModal() {
        document.getElementById("messageModal").style.display = "none";
    }
</script>

