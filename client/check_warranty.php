<?php
// client/check_warranty.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = ''; $error = ''; $validated_item = null;

// Handle Serial Check
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['check_serial'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $serial = trim($_POST['serial_number']);
        
        // 1. Check if it's already registered to someone else
        $check_reg = $pdo->prepare("SELECT id FROM warranty_registrations WHERE serial_number = ? AND status = 'active'");
        $check_reg->execute([$serial]);
        if ($check_reg->fetch()) {
            $error = "This serial number is already registered to an active account.";
        } else {
            // 2. Check if it exists in the owner's Sold Items inventory
            $stmt = $pdo->prepare("
                SELECT si.*, p.product_name, p.model_number, p.brand, p.warranty_terms 
                FROM sold_items si
                JOIN products p ON si.product_id = p.id
                WHERE si.serial_number = ?
            ");
            $stmt->execute([$serial]);
            $validated_item = $stmt->fetch();
            
            if (!$validated_item) {
                $error = "Serial number not found in our records. Please contact support or check the number.";
            } elseif (strtotime($validated_item['warranty_expiry_date']) < time()) {
                $error = "This product's warranty expired on " . date('M d, Y', strtotime($validated_item['warranty_expiry_date']));
            }
        }
    }
}

// Handle Auto-Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalize_registration'])) {
    $sold_item_id = (int)$_POST['sold_item_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM sold_items WHERE id = ? AND is_registered = 0");
    $stmt->execute([$sold_item_id]);
    $item = $stmt->fetch();
    
    if ($item) {
        $warranty_number = generateWarrantyNumber();
        
        $pdo->beginTransaction();
        try {
            // 1. Create the registration automatically
            $reg = $pdo->prepare("
                INSERT INTO warranty_registrations 
                (warranty_number, user_id, product_id, serial_number, purchase_price, purchase_date, warranty_start_date, warranty_end_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $reg->execute([
                $warranty_number, 
                $user_id, 
                $item['product_id'], 
                $item['serial_number'], 
                $item['purchase_price'], // Copied from sold_items
                $item['sale_date'], 
                $item['sale_date'], 
                $item['warranty_expiry_date']
            ]);
            
            // 2. Mark item as registered in inventory
            $pdo->prepare("UPDATE sold_items SET is_registered = 1 WHERE id = ?")->execute([$sold_item_id]);
            
            $pdo->commit();
            $message = "Success! Product registered. Your Warranty #: $warranty_number";
            logActivity($user_id, "Auto-registered warranty #$warranty_number via serial check");
            header("refresh:2;url=dashboard.php");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error finalizing registration: " . $e->getMessage();
        }
    }
}

$page_title = "Check Warranty Coverage";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <div class="form-container" style="max-width: 800px; margin: 0 auto;">
            <h2><i class="fas fa-shield-alt"></i> Verify Your Warranty</h2>
            <p style="color: var(--text-muted); margin-bottom: 25px;">Enter your product serial number below to instantly verify your coverage and link it to your account.</p>

            <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
            <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

            <?php if (!$validated_item || $message): ?>
                <form method="POST" action="" style="display: flex; gap: 10px;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="text" name="serial_number" placeholder="Enter Serial Number (e.g. SN-12345)" required style="flex: 1; font-size: 18px; padding: 15px;">
                    <button type="submit" name="check_serial" class="btn" style="padding: 0 30px;">Check Status</button>
                </form>
            <?php else: ?>
                <div class="stat-card" style="border: 2px solid var(--success); background: rgba(34,197,94,0.05); padding: 30px;">
                    <div style="display: flex; gap: 30px; align-items: flex-start;">
                        <div style="font-size: 50px; color: var(--success);"><i class="fas fa-check-circle"></i></div>
                        <div style="flex: 1;">
                            <h3 style="margin-top: 0; color: var(--success);">Valid Warranty Found!</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                                <div>
                                    <small style="color: #666; font-weight: bold; text-transform: uppercase; font-size: 10px;">Product Name</small>
                                    <div style="font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($validated_item['product_name']); ?></div>
                                </div>
                                <div>
                                    <small style="color: #666; font-weight: bold; text-transform: uppercase; font-size: 10px;">Model Number</small>
                                    <div style="font-size: 18px;"><?php echo htmlspecialchars($validated_item['model_number']); ?></div>
                                </div>
                                <div>
                                    <small style="color: #666; font-weight: bold; text-transform: uppercase; font-size: 10px;">Coverage Ends</small>
                                    <div style="font-size: 18px; color: var(--primary); font-weight: bold;"><?php echo date('M d, Y', strtotime($validated_item['warranty_expiry_date'])); ?></div>
                                </div>
                                <div>
                                    <small style="color: #666; font-weight: bold; text-transform: uppercase; font-size: 10px;">Serial Number</small>
                                    <div style="font-size: 18px;"><code><?php echo htmlspecialchars($validated_item['serial_number']); ?></code></div>
                                </div>
                            </div>

                            <form method="POST" action="" style="margin-top: 30px;">
                                <input type="hidden" name="sold_item_id" value="<?php echo $validated_item['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <button type="submit" name="finalize_registration" class="btn" style="background: var(--success); width: 100%; font-size: 18px; padding: 15px;">
                                    <i class="fas fa-user-plus"></i> Claim This Warranty & Save to Profile
                                </button>
                                <a href="check_warranty.php" style="display: block; text-align: center; margin-top: 15px; color: #666; font-size: 14px;">Not your product? Check another serial</a>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
