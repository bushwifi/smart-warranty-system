<?php
// client/view_warranty.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client' || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$warranty_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT wr.*, p.product_name, p.model_number, p.brand, p.warranty_terms
    FROM warranty_registrations wr
    JOIN products p ON wr.product_id = p.id
    WHERE wr.id = ? AND wr.user_id = ?
");
$stmt->execute([$warranty_id, $user_id]);
$warranty = $stmt->fetch();

if (!$warranty) {
    header("Location: warranties.php");
    exit();
}

$page_title = "View Warranty";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-file-contract"></i> Warranty Details: <?php echo htmlspecialchars($warranty['warranty_number']); ?></h2>
        
        <div style="margin-top:20px; background:#f8f9fa; padding:20px; border-radius:8px;">
            <p><strong>Product:</strong> <?php echo htmlspecialchars($warranty['brand'] . ' ' . $warranty['product_name']); ?></p>
            <p><strong>Model Number:</strong> <?php echo htmlspecialchars($warranty['model_number']); ?></p>
            <p><strong>Serial Number:</strong> <?php echo htmlspecialchars($warranty['serial_number']); ?></p>
            <hr style="margin: 15px 0;">
            <p><strong>Purchase Date:</strong> <?php echo date('M d, Y', strtotime($warranty['purchase_date'])); ?></p>
            <p><strong>Warranty End Date:</strong> <?php echo date('M d, Y', strtotime($warranty['warranty_end_date'])); ?></p>
            <p><strong>Status:</strong> 
                <?php $is_active = strtotime($warranty['warranty_end_date']) >= strtotime(date('Y-m-d')); ?>
                <span class="status status-<?php echo $is_active ? 'active' : 'expired'; ?>"><?php echo $is_active ? 'Active' : 'Expired'; ?></span>
            </p>
        </div>
        
        <div style="margin-top:20px;">
            <h3>Warranty Terms</h3>
            <p style="white-space: pre-wrap; color: #666; font-size: 14px;"><?php echo htmlspecialchars($warranty['warranty_terms']); ?></p>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="warranties.php" class="btn">Back to List</a>
            <?php if($is_active): ?>
                <a href="file_claim.php?warranty_id=<?php echo $warranty['id']; ?>" class="btn" style="background:#28a745;">File a Claim for this Product</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
