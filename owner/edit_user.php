<?php
// owner/edit_user.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Fetch staff details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'technician'");
$stmt->execute([$id]);
$u = $stmt->fetch();

if (!$u) {
    header("Location: users.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $new_password = $_POST['new_password'];

        try {
            $pdo->beginTransaction();
            
            // Basic updates
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $phone, $id]);
            
            // Password update if provided
            if (!empty($new_password)) {
                if (strlen($new_password) < 6) {
                    throw new Exception("New password must be at least 6 characters.");
                }
                $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $id]);
            }
            
            $pdo->commit();
            logActivity($owner_id, "Updated staff profile #$id: $full_name");
            $message = "Staff profile updated successfully!";
            header("refresh:1;url=users.php");
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = "Error updating profile: " . $e->getMessage();
        }
    }
}

$page_title = "Edit Staff Profile";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <a href="users.php" class="btn btn-sm" style="margin-bottom: 20px; background: #64748b;"><i class="fas fa-arrow-left"></i> Back to Staff List</a>
        
        <h2><i class="fas fa-user-edit"></i> Edit Technician Profile: <?php echo htmlspecialchars($u['full_name']); ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Update contact details or reset the password for this technician account.</p>

        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" class="form-container" style="max-width: 600px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label>Username (ReadOnly)</label>
                <input type="text" value="@<?php echo htmlspecialchars($u['username']); ?>" disabled style="background: #f1f5f9; font-family: monospace;">
            </div>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($u['full_name']); ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($u['phone']); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                <label style="color: var(--primary);"><i class="fas fa-key"></i> Administrative Password Reset</label>
                <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">Leave blank to keep the current password.</p>
                <input type="password" name="new_password" placeholder="Enter new secure password (min 6 chars)" minlength="6">
            </div>

            <button type="submit" name="update_user" class="btn" style="width: 100%; margin-top: 25px;">Update Account Details</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
