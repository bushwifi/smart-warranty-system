<?php
// owner/edit_sold_item.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$item_id = (int)($_GET['id'] ?? 0);
$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Fetch item details
$stmt = $pdo->prepare("SELECT si.*, p.product_name FROM sold_items si JOIN products p ON si.product_id = p.id WHERE si.id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: manage_sold_items.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $new_serial = trim($_POST['serial_number']);
        $new_price = (float)$_POST['purchase_price'];
        $new_sale_date = $_POST['sale_date'];
        
        // Recalculate expiry based on product warranty period
        $p_stmt = $pdo->prepare("SELECT warranty_period_months FROM products WHERE id = ?");
        $p_stmt->execute([$item['product_id']]);
        $prod = $p_stmt->fetch();
        $new_expiry = date('Y-m-d', strtotime($new_sale_date . " + {$prod['warranty_period_months']} months"));

        try {
            $pdo->prepare("
                UPDATE sold_items 
                SET serial_number = ?, purchase_price = ?, sale_date = ?, warranty_expiry_date = ?
                WHERE id = ?
            ")->execute([$new_serial, $new_price, $new_sale_date, $new_expiry, $item_id]);
            
            // If already registered, update the registration price too for consistency
            if ($item['is_registered']) {
                $pdo->prepare("UPDATE warranty_registrations SET purchase_price = ? WHERE serial_number = ?")->execute([$new_price, $item['serial_number']]);
            }

            logActivity($owner_id, "Updated inventory item #$item_id (New Serial: $new_serial)");
            $message = "Inventory item updated successfully!";
            header("refresh:1;url=manage_sold_items.php");
        } catch (PDOException $e) {
            $error = "Error updating item: " . $e->getMessage();
        }
    }
}

$page_title = "Edit Inventory Item";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <a href="manage_sold_items.php" class="btn btn-sm" style="margin-bottom: 20px; background: #64748b;"><i class="fas fa-arrow-left"></i> Back to Inventory</a>
        
        <h2><i class="fas fa-edit"></i> Edit Inventory Record: <?php echo htmlspecialchars($item['serial_number']); ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Updating this record will automatically recalculate the warranty expiry date based on the new sale date.</p>

        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" class="form-container" style="max-width: 600px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label>Product (ReadOnly)</label>
                <input type="text" value="<?php echo htmlspecialchars($item['product_name']); ?>" disabled style="background: #f1f5f9;">
            </div>

            <div class="form-group">
                <label>Serial Number</label>
                <input type="text" name="serial_number" value="<?php echo htmlspecialchars($item['serial_number']); ?>" required>
            </div>

            <div class="form-group">
                <label>Sale Price (<?php echo CURRENCY; ?>)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 12px; color: #94a3b8; font-size: 11px; font-weight: bold;"><?php echo CURRENCY; ?></span>
                    <input type="number" step="0.01" name="purchase_price" value="<?php echo $item['purchase_price']; ?>" required style="padding-left: 35px;">
                </div>
            </div>

            <div class="form-group">
                <label>Sale Date</label>
                <input type="date" name="sale_date" value="<?php echo $item['sale_date']; ?>" required>
            </div>

            <div style="margin-top: 25px; padding: 15px; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 4px;">
                <i class="fas fa-info-circle"></i> <strong>Current Expiry:</strong> <?php echo date('M d, Y', strtotime($item['warranty_expiry_date'])); ?>
            </div>

            <button type="submit" name="update_item" class="btn" style="width: 100%; margin-top: 25px;">Save Changes & Recalculate Expiry</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
