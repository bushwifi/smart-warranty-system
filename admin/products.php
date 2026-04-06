<?php
// admin/products.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = '';
$error = '';

// Handle add product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $product_name = trim($_POST['product_name']);
        $model_number = trim($_POST['model_number']);
        $brand = trim($_POST['brand']);
        $warranty_months = (int)$_POST['warranty_period_months'];
        $terms = trim($_POST['warranty_terms']);
        
        $stmt = $pdo->prepare("INSERT INTO products (product_name, model_number, brand, warranty_period_months, warranty_terms) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name, $model_number, $brand, $warranty_months, $terms])) {
            $message = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    }
}

// Get all products
$products = $pdo->query("SELECT * FROM products ORDER BY brand, product_name")->fetchAll();

$page_title = "Manage Products";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-plus-circle"></i> Add New Product</h2>
        <?php if($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" style="max-width: 600px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" required>
            </div>
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Model Number</label>
                <input type="text" name="model_number" required>
            </div>
            <div class="form-group">
                <label>Warranty Period (Months)</label>
                <input type="number" name="warranty_period_months" min="1" max="120" value="12" required>
            </div>
            <div class="form-group">
                <label>Warranty Terms</label>
                <textarea name="warranty_terms" rows="4">Standard manufacturer warranty applies.</textarea>
            </div>
            <button type="submit" name="add_product" class="btn">Add Product</button>
        </form>
    </div>

    <div class="section">
        <h2><i class="fas fa-box"></i> Product Catalog</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Product Name</th>
                        <th>Model #</th>
                        <th>Warranty (Months)</th>
                        <th>Added On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['brand']); ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($product['model_number']); ?></td>
                            <td><?php echo htmlspecialchars($product['warranty_period_months']); ?> months</td>
                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
