<?php
// owner/edit_product.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $brand = trim($_POST['brand']);
        $product_name = trim($_POST['product_name']);
        $model_number = trim($_POST['model_number']);
        $price = (float)$_POST['price'];
        $warranty_months = (int)$_POST['warranty_period_months'];
        $terms = trim($_POST['warranty_terms']);

        try {
            $pdo->prepare("
                UPDATE products 
                SET brand = ?, product_name = ?, model_number = ?, price = ?, warranty_period_months = ?, warranty_terms = ?
                WHERE id = ?
            ")->execute([$brand, $product_name, $model_number, $price, $warranty_months, $terms, $id]);
            
            logActivity($owner_id, "Updated product #$id: $product_name");
            $message = "Product specification updated successfully!";
            header("refresh:1;url=products.php");
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
        }
    }
}

$page_title = "Edit Product Specification";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <a href="products.php" class="btn btn-sm" style="margin-bottom: 20px; background: #64748b;"><i class="fas fa-arrow-left"></i> Back to Catalog</a>
        
        <h2><i class="fas fa-edit"></i> Edit Product: <?php echo htmlspecialchars($product['product_name']); ?></h2>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Refine product details or adjust warranty terms for future registrations.</p>

        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" class="form-container" style="max-width: 700px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Model Number</label>
                    <input type="text" name="model_number" value="<?php echo htmlspecialchars($product['model_number']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Base Retail Price (<?php echo CURRENCY; ?>)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Warranty Period (Months)</label>
                    <input type="number" name="warranty_period_months" value="<?php echo $product['warranty_period_months']; ?>" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label>Warranty Terms & Conditions</label>
                <textarea name="warranty_terms" rows="6" required><?php echo htmlspecialchars($product['warranty_terms']); ?></textarea>
            </div>

            <button type="submit" name="update_product" class="btn" style="width: 100%; margin-top: 10px;">Update Catalog Entry</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
