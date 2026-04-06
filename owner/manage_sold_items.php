<?php
// owner/manage_sold_items.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Handle adding sold item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_sold_item'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $product_id = (int)$_POST['product_id'];
        $serial_number = trim($_POST['serial_number']);
        $purchase_price = (float)$_POST['purchase_price'];
        $sale_date = $_POST['sale_date'];
        
        // Fetch product warranty period to calculate expiry
        $stmt = $pdo->prepare("SELECT warranty_period_months FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            $expiry_date = date('Y-m-d', strtotime($sale_date . " + {$product['warranty_period_months']} months"));
            
            try {
                $insert = $pdo->prepare("
                    INSERT INTO sold_items (product_id, serial_number, purchase_price, sale_date, warranty_expiry_date)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insert->execute([$product_id, $serial_number, $purchase_price, $sale_date, $expiry_date]);
                $message = "Sold item registered successfully! Serial: $serial_number";
                logActivity($owner_id, "Added pre-sold serial: $serial_number");
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = "This serial number is already registered in the inventory.";
                } else {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        } else {
            $error = "Invalid product selected.";
        }
    }
}

// Get all products for dropdown
$products = $pdo->query("SELECT id, product_name, model_number FROM products ORDER BY product_name")->fetchAll();

// Get recently sold items
$sold_items = $pdo->query("
    SELECT si.*, p.product_name, p.model_number 
    FROM sold_items si
    JOIN products p ON si.product_id = p.id
    ORDER BY si.created_at DESC
    LIMIT 100
")->fetchAll();

$page_title = "Sold Items Inventory";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-barcode"></i> Add Sold Item to Inventory</h2>
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" class="form-container" style="max-width: 100%; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label>Product</label>
                <select name="product_id" required>
                    <option value="">Select product...</option>
                    <?php foreach($products as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['product_name']); ?> (<?php echo $p['model_number']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Serial Number</label>
                <input type="text" name="serial_number" placeholder="Enter Serial" required>
            </div>

            <div class="form-group">
                <label>Sale Date</label>
                <input type="date" name="sale_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Purchase Price (<?php echo CURRENCY; ?>)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 12px; color: #94a3b8; font-size: 11px; font-weight: bold;"><?php echo CURRENCY; ?></span>
                    <input type="number" step="0.01" name="purchase_price" placeholder="Price" required style="padding-left: 35px;">
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" name="add_sold_item" class="btn" style="width: 100%; height: 45px;">Add to Inventory</button>
            </div>
        </form>
    </div>

    <div class="section">
        <h2><i class="fas fa-list"></i> Pre-Sold Inventory (Recently Added)</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Serial Number</th>
                        <th>Sale Date</th>
                        <th>Warranty Expiry</th>
                        <th>Client Status</th>
                        <th>Added On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($sold_items) > 0): ?>
                        <?php foreach($sold_items as $si): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($si['product_name']); ?></strong><br><small><?php echo htmlspecialchars($si['model_number']); ?></small></td>
                                <td><code><?php echo htmlspecialchars($si['serial_number']); ?></code></td>
                                <td><?php echo date('M d, Y', strtotime($si['sale_date'])); ?></td>
                                <td>
                                    <span class="badge <?php echo strtotime($si['warranty_expiry_date']) < time() ? 'bg-danger' : 'bg-success'; ?>">
                                        <?php echo date('M d, Y', strtotime($si['warranty_expiry_date'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($si['is_registered']): ?>
                                        <span class="badge bg-info"><i class="fas fa-user-check"></i> Registered</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Unclaimed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($si['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="edit_sold_item.php?id=<?php echo $si['id']; ?>" class="btn btn-sm" title="Edit Item" style="background: var(--primary); padding: 5px 10px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if(!$si['is_registered']): ?>
                                            <form method="POST" action="delete_sold_item.php" onsubmit="return confirm('Are you sure you want to remove this item from inventory?');" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                                <input type="hidden" name="item_id" value="<?php echo $si['id']; ?>">
                                                <button type="submit" class="btn btn-sm" style="background: #ef4444; padding: 5px 10px;" title="Delete Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm" disabled style="background: #cbd5e1; cursor: not-allowed; padding: 5px 10px;" title="Cannot delete registered item">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="empty-state">No items in the sold inventory yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
