<?php
// owner/delete_user.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security token validation failed.";
    } else {
        $target_id = (int)$_POST['user_id'];
        
        // 1. Check for active assignments
        $check_claims = $pdo->prepare("SELECT COUNT(*) FROM claims WHERE assigned_technician_id = ? AND status NOT IN ('completed', 'refunded', 'replaced', 'rejected')");
        $check_claims->execute([$target_id]);
        $active_count = $check_claims->fetchColumn();
        
        if ($active_count > 0) {
            $_SESSION['error'] = "Cannot delete technician. They still have $active_count active claim assignments. Please reassign their work first.";
        } else {
            // 2. Perform Hard Delete
            $pdo->prepare("DELETE FROM users WHERE id = ? AND user_type = 'technician'")->execute([$target_id]);
            logActivity($_SESSION['user_id'], "Permanently deleted technician account #$target_id");
            $_SESSION['message'] = "Staff account deleted successfully.";
        }
    }
}

header("Location: users.php");
exit();
