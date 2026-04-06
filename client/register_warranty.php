<?php
// client/register_warranty.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$message = '';
$error = '';

// Get available products
$products = $pdo->query("SELECT * FROM products ORDER BY product_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_warranty'])) {
    $serial_number = $_POST['serial_number'];
    $purchase_date = $_POST['purchase_date'];
    $purchase_price = (float)$_POST['purchase_price'];
    $quantity = (int)$_POST['quantity'];
    
    // Get product warranty period
    $stmt = $pdo->prepare("SELECT warranty_period_months FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Calculate warranty dates
        $warranty_start = $purchase_date;
        $warranty_end = date('Y-m-d', strtotime($purchase_date . " + {$product['warranty_period_months']} months"));
        
        // Handle file upload
        $receipt_path = '';
        if (isset($_FILES['purchase_receipt']) && $_FILES['purchase_receipt']['error'] == 0) {
            $target_dir = "../uploads/receipts/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['purchase_receipt']['name'], PATHINFO_EXTENSION);
            $receipt_path = 'uploads/receipts/' . uniqid() . '.' . $file_extension;
            $target_file = "../" . $receipt_path;
            
            if (!move_uploaded_file($_FILES['purchase_receipt']['tmp_name'], $target_file)) {
                $error = "Failed to upload receipt.";
            }
        }
        
        if (empty($error)) {
            $warranty_number = generateWarrantyNumber();
            
            $insert = $pdo->prepare("
                INSERT INTO warranty_registrations 
                (warranty_number, user_id, product_id, serial_number, purchase_date, purchase_price, quantity, purchase_receipt, warranty_start_date, warranty_end_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($insert->execute([$warranty_number, $user_id, $product_id, $serial_number, $purchase_date, $purchase_price, $quantity, $receipt_path, $warranty_start, $warranty_end])) {
                $message = "Warranty registered successfully! Your warranty number is: $warranty_number";
                
                // Create notification
                createNotification($user_id, 'Warranty Registered', "Your warranty #$warranty_number has been registered successfully.", 'success');
                
                // Log activity
                logActivity($user_id, "Registered warranty #$warranty_number");
            } else {
                $error = "Error registering warranty.";
            }
        }
    } else {
         $error = "Invalid product selection.";
    }
}

$page_title = "Register Warranty";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-file-signature"></i> Register New Warranty</h2>
        
        <?php if($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select Product</label>
                <select name="product_id" required>
                    <option value="">Choose a product...</option>
                    <?php foreach($products as $product): ?>
                        <option value="<?php echo htmlspecialchars($product['id']); ?>">
                            <?php echo htmlspecialchars($product['product_name']); ?> (<?php echo htmlspecialchars($product['model_number']); ?>) - <?php echo htmlspecialchars($product['warranty_period_months']); ?> months warranty
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Serial Number</label>
                <input type="text" name="serial_number" placeholder="Enter product serial number" required>
            </div>
            
            <div class="form-group">
                <label>Purchase Date</label>
                <input type="date" name="purchase_date" max="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 2;">
                    <label>Purchase Price (per item)</label>
                    <input type="number" name="purchase_price" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Quantity</label>
                    <input type="number" name="quantity" min="1" value="1" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Purchase Receipt (Optional)</label>
                <input type="file" name="purchase_receipt" accept=".pdf,.jpg,.jpeg,.png">
                <div class="file-info" style="font-size: 12px; color: #999; margin-top: 5px;">Accepted formats: PDF, JPG, PNG (Max size: 5MB)</div>
            </div>
            
            <button type="submit" name="register_warranty" class="btn">
                <i class="fas fa-save"></i> Register Warranty
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
