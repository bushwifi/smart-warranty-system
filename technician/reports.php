<?php
// technician/reports.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'technician') {
    header("Location: ../index.php");
    exit();
}

$technician_id = (int)$_SESSION['user_id'];

// Get monthly verifications for chart
$monthly_verifications = $pdo->query("
    SELECT DATE_FORMAT(verification_date, '%Y-%m') as month,
           COUNT(*) as total_verified,
           SUM(CASE WHEN is_valid = 1 THEN 1 ELSE 0 END) as valid,
           SUM(CASE WHEN is_valid = 0 THEN 1 ELSE 0 END) as invalid
    FROM claim_verification
    WHERE technician_id = $technician_id
    GROUP BY DATE_FORMAT(verification_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
")->fetchAll();

$page_title = "My Performance Reports";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-chart-bar"></i> Performance Metrics</h2>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Claims Reviewed</th>
                        <th>Valid Approvals</th>
                        <th>Rejections</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($monthly_verifications) > 0): ?>
                        <?php foreach($monthly_verifications as $mv): ?>
                            <tr>
                                <td><strong><?php echo date('F Y', strtotime($mv['month'] . '-01')); ?></strong></td>
                                <td><?php echo htmlspecialchars($mv['total_verified']); ?></td>
                                <td><span style="color: #28a745;"><?php echo htmlspecialchars($mv['valid']); ?></span></td>
                                <td><span style="color: #dc3545;"><?php echo htmlspecialchars($mv['invalid']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty-state">No data available yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
