<?php
// owner/reports.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

// Get cost analysis (mock data based on actual claims resolution)
$cost_analysis = $pdo->query("
    SELECT 
        DATE_FORMAT(c.created_at, '%Y-%m') as month,
        COUNT(c.id) as total_claims,
        SUM(wr.quantity) as total_items,
        SUM(CASE WHEN cr.resolution_type = 'repair' THEN 50.00 ELSE 0 END) as estimated_repair_cost,
        SUM(CASE WHEN cr.resolution_type = 'replacement' THEN wr.purchase_price * wr.quantity ELSE 0 END) as estimated_replacement_cost,
        SUM(IFNULL(cr.refund_amount, 0)) as actual_refunds
    FROM claims c
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    LEFT JOIN claim_resolution cr ON c.id = cr.claim_id
    GROUP BY DATE_FORMAT(c.created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
")->fetchAll();

$page_title = "Financial Reports";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-chart-bar"></i> Monthly Cost Analysis</h2>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Claims Processed</th>
                        <th>Items Handled</th>
                        <th>Est. Repair Costs</th>
                        <th>Est. Replacement Value</th>
                        <th>Total Cash Refunds</th>
                        <th>Total Liability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($cost_analysis) > 0): ?>
                        <?php foreach($cost_analysis as $ca): 
                            $total_liability = $ca['estimated_repair_cost'] + $ca['estimated_replacement_cost'] + $ca['actual_refunds'];
                        ?>
                            <tr>
                                <td><strong><?php echo date('F Y', strtotime($ca['month'] . '-01')); ?></strong></td>
                                <td><?php echo htmlspecialchars($ca['total_claims']); ?></td>
                                <td><?php echo htmlspecialchars($ca['total_items']); ?></td>
                                <td><?php echo CURRENCY; ?><?php echo number_format($ca['estimated_repair_cost'], 2); ?></td>
                                <td><?php echo CURRENCY; ?><?php echo number_format($ca['estimated_replacement_cost'], 2); ?></td>
                                <td style="color: #dc3545;"><?php echo CURRENCY; ?><?php echo number_format($ca['actual_refunds'], 2); ?></td>
                                <td><strong><?php echo CURRENCY; ?><?php echo number_format($total_liability, 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-state">No financial data available to report.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
