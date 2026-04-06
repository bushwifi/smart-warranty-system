<?php
// owner/products.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
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
        $price = (float)$_POST['price'];
        $warranty_months = (int)$_POST['warranty_period_months'];
        $terms = trim($_POST['warranty_terms']);
        
        $stmt = $pdo->prepare("INSERT INTO products (product_name, model_number, brand, price, warranty_period_months, warranty_terms) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name, $model_number, $brand, $price, $warranty_months, $terms])) {
            $message = "Product added to the catalog successfully!";
        } else {
            $error = "Failed to add product to catalog.";
        }
    }
}

// Filter logic
$search = trim($_GET['search'] ?? '');
$filter_brand = trim($_GET['brand'] ?? '');

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (product_name LIKE ? OR model_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter_brand) {
    $query .= " AND brand = ?";
    $params[] = $filter_brand;
}

$query .= " ORDER BY brand, product_name";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get unique brands for the filter dropdown
$unique_brands = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand")->fetchAll(PDO::FETCH_COLUMN);

$page_title = "Product Catalog Management";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div style="display: flex; gap: 30px; align-items: flex-start; flex-wrap: wrap;">
        <!-- Left Sidebar: Add & Filters -->
        <div style="flex: 0 0 350px; position: sticky; top: 20px;">
            <!-- FILTER SECTION -->
            <div class="section" style="margin-bottom: 25px; border: 1px solid rgba(0,0,0,0.05); background: #f8fafc;">
                <h3 style="margin-top: 0; font-size: 18px;"><i class="fas fa-filter"></i> Advanced Filters</h3>
                <form method="GET" action="" style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold;">Search Catalog</label>
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name or model..." style="padding-left: 35px; width: 100%;">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: bold;">Brand Selection</label>
                        <select name="brand" style="width: 100%;">
                            <option value="">All Digital Brands</option>
                            <?php foreach($unique_brands as $b): ?>
                                <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $filter_brand == $b ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <button type="submit" class="btn" style="flex: 1; background: var(--primary);">Apply Filters</button>
                        <?php if($search || $filter_brand): ?>
                            <a href="products.php" class="btn" style="background: #64748b; color: white;" title="Reset"><i class="fas fa-undo"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- ADD PRODUCT SECTION -->
            <div class="section" style="border: 1px solid rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; font-size: 18px;"><i class="fas fa-plus-circle"></i> Add New Product</h3>
                <?php if($message): ?><div class="message success" style="padding: 10px; font-size: 13px;"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
                <?php if($error): ?><div class="message error" style="padding: 10px; font-size: 13px;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                
                <form method="POST" action="" class="form-container" style="display: flex; flex-direction: column; gap: 12px;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px;">Brand</label>
                        <input type="text" name="brand" required placeholder="e.g. Samsung">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px;">Product Name</label>
                        <input type="text" name="product_name" required placeholder="e.g. 55-inch QLED">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px;">Model #</label>
                        <input type="text" name="model_number" required placeholder="e.g. QE55Q70A">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px;">Base Price (<?php echo CURRENCY; ?>)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 10px; top: 12px; color: #94a3b8; font-size: 11px; font-weight: bold;"><?php echo CURRENCY; ?></span>
                            <input type="number" step="0.01" name="price" placeholder="0.00" required style="padding-left: 35px;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-size:12px;">Warranty (Months)</label>
                        <input type="number" name="warranty_period_months" min="1" max="120" value="12" required>
                    </div>
                    <button type="submit" name="add_product" class="btn" style="width: 100%; margin-top: 10px; background: #10b981; border: none;">Save to Catalog</button>
                </form>
            </div>
        </div>

        <!-- Right Main: The Table -->
        <div style="flex: 1; min-width: 500px;">
            <div class="section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;"><i class="fas fa-box"></i> Official Product Catalog</h2>
                    <div style="font-size: 13px; color: #64748b; font-weight: bold; background: #f1f5f9; padding: 5px 15px; border-radius: 20px;">
                        Showing: <?php echo count($products); ?> Products
                    </div>
                </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Product Name</th>
                        <th>Model #</th>
                        <th>Warranty (Months)</th>
                        <th>Base Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['brand']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><code><?php echo htmlspecialchars($product['model_number']); ?></code></td>
                            <td><?php echo htmlspecialchars($product['warranty_period_months']); ?> months</td>
                            <td><strong><?php echo CURRENCY; ?><?php echo number_format($product['price'], 2); ?></strong></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm" style="background: var(--primary); padding: 5px 10px;" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="delete_product.php" onsubmit="return confirm('Are you sure? This will remove the product definition from the catalog.');" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="btn btn-sm" style="background: #ef4444; padding: 5px 10px;" title="Delete Product">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>
