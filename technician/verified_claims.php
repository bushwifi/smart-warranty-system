<?php
// technician/verified_claims.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'technician') {
    header("Location: ../index.php");
    exit();
}

$technician_id = (int)$_SESSION['user_id'];

$verifications = $pdo->query("
    SELECT cv.*, c.claim_number, c.status as claim_status, u.full_name as customer_name,
           p.product_name, c.created_at as filed_date
    FROM claim_verification cv
    JOIN claims c ON cv.claim_id = c.id
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE cv.technician_id = $technician_id
    ORDER BY cv.verification_date DESC
")->fetchAll();

$page_title = "Verified Claims History";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-check-circle"></i> Verification History</h2>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Claim #</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Valid</th>
                        <th>Recommendation</th>
                        <th>Status</th>
                        <th>Verified On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($verifications) > 0): ?>
                        <?php foreach($verifications as $ver): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ver['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($ver['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($ver['product_name']); ?></td>
                                <td>
                                    <?php if($ver['is_valid']): ?>
                                        <span style="color: #28a745;"><i class="fas fa-check-circle"></i> Yes</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545;"><i class="fas fa-times-circle"></i> No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(ucfirst($ver['recommended_action'])); ?></td>
                                <td><span class="status status-<?php echo htmlspecialchars($ver['claim_status']); ?>"><?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($ver['claim_status']))); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($ver['verification_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="empty-state">No verifications on record.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
