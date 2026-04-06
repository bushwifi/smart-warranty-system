<?php
// owner/delete_sold_item.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security token validation failed.";
    } else {
        $item_id = (int)$_POST['item_id'];
        $owner_id = (int)$_SESSION['user_id'];
        
        // 1. Check if item exists and is NOT registered
        $stmt = $pdo->prepare("SELECT serial_number, is_registered FROM sold_items WHERE id = ?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();
        
        if (!$item) {
            $_SESSION['error'] = "Item not found.";
        } elseif ($item['is_registered']) {
            $_SESSION['error'] = "Error: Item '{$item['serial_number']}' cannot be deleted as it is already registered to a client.";
        } else {
            // 2. Perform Hard Delete
            $serial = $item['serial_number'];
            $pdo->prepare("DELETE FROM sold_items WHERE id = ?")->execute([$item_id]);
            logActivity($owner_id, "Deleted pre-sold serial number from inventory: $serial");
            $_SESSION['message'] = "Item '{$serial}' deleted from inventory successfully.";
        }
    }
}

header("Location: manage_sold_items.php");
exit();
