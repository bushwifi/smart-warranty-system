<?php
// admin/settings.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        // Demo implementation. A real app would save this to DB.
        $message = "System settings successfully updated.";
    }
}

$page_title = "System Settings";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-cog"></i> Global Configuration</h2>
        
        <?php if($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label>Global Site Name</label>
                <input type="text" name="site_name" value="<?php echo htmlspecialchars(SITE_NAME); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Admin Contact Email</label>
                <input type="email" name="admin_email" value="admin@warranty.system.test" required>
            </div>
            
            <div class="form-group" style="margin-top: 20px;">
                <label style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" name="maintenance_mode" style="width: auto;">
                    Enable Maintenance Mode (Takes site offline for clients)
                </label>
            </div>
            
            <button type="submit" name="save_settings" class="btn">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
