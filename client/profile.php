<?php
// client/profile.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        if($stmt->execute([$full_name, $email, $phone, $address, $user_id])) {
            $_SESSION['full_name'] = $full_name;
            $message = "Profile successfully updated.";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Fetch current details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$page_title = "My Profile";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-user-cog"></i> Edit Profile</h2>
        
        <?php if($message): ?><div class="message success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="address" required rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <button type="submit" name="update_profile" class="btn">Save Changes</button>
        </form>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
