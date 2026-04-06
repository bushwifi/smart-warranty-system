<?php
// owner/dashboard.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

// Get statistics
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users WHERE user_type = 'client') as total_customers,
        (SELECT COUNT(*) FROM warranty_registrations WHERE status = 'active') as active_warranties,
        (SELECT COUNT(*) FROM claims WHERE status = 'pending_refund') as pending_refunds,
        (SELECT COUNT(*) FROM claims WHERE status = 'rejected') as rejected_claims,
        (SELECT COUNT(*) FROM claims WHERE status = 'completed') as resolved_claims,
        (SELECT COUNT(*) FROM claims) as total_claims,
        (SELECT COUNT(*) FROM claim_resolution WHERE resolution_type = 'repair') as repairs_done,
        (SELECT COUNT(*) FROM claim_resolution WHERE resolution_type = 'replacement') as replacements_done,
        (SELECT COUNT(id) FROM claim_resolution WHERE resolution_type = 'refund') as refunds_done,
        (SELECT SUM(refund_amount) FROM claim_resolution WHERE resolution_type = 'refund') as total_refunds
")->fetch();

// Calculate metrics
$total_claims_count = $stats['total_claims'] ?: 1;
$success_rate = ($stats['resolved_claims']) / $total_claims_count * 100;
$rejection_rate = $stats['rejected_claims'] / $total_claims_count * 100;

// Get monthly claims data for chart
$monthly_claims = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as total_claims,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM claims
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
")->fetchAll();

// Get top products by claims
$top_products = $pdo->query("
    SELECT 
        p.product_name,
        COUNT(c.id) as claim_count,
        COUNT(DISTINCT c.user_id) as unique_customers
    FROM products p
    JOIN warranty_registrations wr ON p.id = wr.product_id
    JOIN claims c ON wr.id = c.warranty_id
    GROUP BY p.id
    ORDER BY claim_count DESC
    LIMIT 5
")->fetchAll();

// Get recent activities
$recent_activities = $pdo->query("
    SELECT * FROM activity_logs 
    ORDER BY created_at DESC 
    LIMIT 10
")->fetchAll();

$page_title = "Business Intelligence";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-users icon"></i>
            <h3>Total Customers</h3>
            <div class="value"><?php echo number_format($stats['total_customers'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-shield-alt icon"></i>
            <h3>Active Warranties</h3>
            <div class="value"><?php echo number_format($stats['active_warranties'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock icon"></i>
            <h3>Pending Claims</h3>
            <div class="value"><?php echo number_format($stats['pending_claims'] ?? 0); ?></div>
        </div>
        <div class="stat-card featured" style="border-left: 5px solid #28a745;">
            <i class="fas fa-check-double icon" style="color: #28a745;"></i>
            <h3>Resolved Claims</h3>
            <div class="value"><?php echo number_format($stats['resolved_claims'] ?? 0); ?></div>
            <div style="font-size: 11px; margin-top: 5px; color: #64748b; font-weight: bold;">Final Success Rate: <?php echo number_format($success_rate, 1); ?>%</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-wrench icon"></i>
            <h3>Repairs Done</h3>
            <div class="value"><?php echo number_format($stats['repairs_done'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-exchange-alt icon"></i>
            <h3>Replacements</h3>
            <div class="value"><?php echo number_format($stats['replacements_done'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-handshake icon"></i>
            <h3>Refunds Issued</h3>
            <div class="value"><?php echo number_format($stats['refunds_done'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-ban icon" style="color: #dc3545;"></i>
            <h3>Rejection Rate</h3>
            <div class="value"><?php echo number_format($rejection_rate, 1); ?>%</div>
        </div>
        <div class="stat-card" style="background: var(--bg-warning); border: 1px solid var(--warning);">
            <i class="fas fa-hand-holding-usd icon" style="color: var(--warning);"></i>
            <h3>Pending Refunds</h3>
            <div class="value" style="color: var(--warning);"><?php echo number_format($stats['pending_refunds'] ?? 0); ?></div>
            <a href="refund_approvals.php" style="font-size: 11px; text-decoration: underline; color: var(--warning);">View & Authorize</a>
        </div>
        <div class="stat-card featured" style="border-left: 5px solid #ffc107;">
            <i class="fas fa-dollar-sign icon" style="color: #ffc107;"></i>
            <h3>Authorized Refunded Value</h3>
            <div class="value" style="font-size: 2.5rem;"><?php echo CURRENCY; ?><?php echo number_format($stats['total_refunds'] ?? 0, 2); ?></div>
            <div style="font-size: 11px; margin-top: 5px; color: #64748b; font-weight: bold;">Total Transactions: <?php echo number_format($stats['total_claims']); ?></div>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-chart-line"></i> Claims Trend (Last 6 Months)</h2>
        <div class="chart-container" style="max-width: 100%; margin: 20px 0;">
            <canvas id="claimsChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-fire"></i> Top Products by Claims</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Number of Claims</th>
                    <th>Unique Customers</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($top_products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['claim_count']); ?></td>
                    <td><?php echo htmlspecialchars($product['unique_customers']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-history"></i> Recent System Activities</h2>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_activities as $activity): ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['user_id'] ?? 'System'); ?></td>
                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                    <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                    <td><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for chart
    const monthlyData = <?php 
        $months = [];
        $totals = [];
        $approved = [];
        $rejected = [];
        
        foreach($monthly_claims as $row) {
            array_unshift($months, date('M Y', strtotime($row['month'] . '-01')));
            array_unshift($totals, $row['total_claims']);
            array_unshift($approved, $row['approved']);
            array_unshift($rejected, $row['rejected']);
        }
        
        echo json_encode([
            'months' => $months,
            'totals' => $totals,
            'approved' => $approved,
            'rejected' => $rejected
        ]);
    ?>;
    
    const ctx = document.getElementById('claimsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.months,
            datasets: [
                {
                    label: 'Total Claims',
                    data: monthlyData.totals,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Approved',
                    data: monthlyData.approved,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Rejected',
                    data: monthlyData.rejected,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
