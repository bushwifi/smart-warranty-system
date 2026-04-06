<?php
// client/claims.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$claims = $pdo->query("
    SELECT c.*, p.product_name, wr.warranty_number 
    FROM claims c
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE c.user_id = $user_id 
    ORDER BY c.created_at DESC
")->fetchAll();

$page_title = "My Claims";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main-content">
    <div class="section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;"><i class="fas fa-list"></i> My Warranty Claims</h2>
            <a href="file_claim.php" class="btn"><i class="fas fa-plus"></i> File a Claim</a>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                         <th>Claim #</th>
                         <th>Product</th>
                         <th>Category</th>
                         <th>Date Filed</th>
                         <th>Status</th>
                         <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($claims) > 0): ?>
                        <?php foreach($claims as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($claim['issue_category'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                                <td><span class="status status-<?php echo htmlspecialchars($claim['status']); ?>"><?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($claim['status']))); ?></span></td>
                                <td><a href="view_claim.php?id=<?php echo htmlspecialchars($claim['id']); ?>" class="btn btn-sm">Details</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-state">No claims filed yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
