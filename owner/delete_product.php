<?php
// owner/delete_product.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security token validation failed.";
    } else {
        $product_id = (int)$_POST['product_id'];
        
        // 1. Check for dependencies in Sold Items or Active Warranties
        $check_sold = $pdo->prepare("SELECT COUNT(*) FROM sold_items WHERE product_id = ?");
        $check_sold->execute([$product_id]);
        $sold_count = $check_sold->fetchColumn();
        
        $check_reg = $pdo->prepare("SELECT COUNT(*) FROM warranty_registrations WHERE product_id = ?");
        $check_reg->execute([$product_id]);
        $reg_count = $check_reg->fetchColumn();
        
        if ($sold_count > 0 || $reg_count > 0) {
            $_SESSION['error'] = "Cannot delete product. There are $sold_count items in inventory and $reg_count active registrations linked to this product.";
        } else {
            // 2. Perform Hard Delete
            $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$product_id]);
            logActivity($_SESSION['user_id'], "Deleted product ID #$product_id from catalog");
            $_SESSION['message'] = "Product removed from catalog successfully.";
        }
    }
}

header("Location: products.php");
exit();
