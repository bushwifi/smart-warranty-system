<?php
// client/dashboard.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Get statistics
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM warranty_registrations WHERE user_id = $user_id AND status = 'active') as active_warranties,
        (SELECT COUNT(*) FROM warranty_registrations WHERE user_id = $user_id AND status = 'expired') as expired_warranties,
        (SELECT COUNT(*) FROM claims WHERE user_id = $user_id AND status = 'pending') as pending_claims,
        (SELECT COUNT(*) FROM claims WHERE user_id = $user_id AND status = 'completed') as completed_claims
")->fetch();

// Get recent warranties
$recent_warranties = $pdo->query("
    SELECT wr.*, p.product_name, p.model_number, p.brand
    FROM warranty_registrations wr
    JOIN products p ON wr.product_id = p.id
    WHERE wr.user_id = $user_id
    ORDER BY wr.created_at DESC
    LIMIT 5
")->fetchAll();

// Get recent claims with resolution details
$recent_claims = $pdo->query("
    SELECT c.*, p.product_name, cr.resolution_type
    FROM claims c
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    LEFT JOIN claim_resolution cr ON c.id = cr.claim_id
    WHERE c.user_id = $user_id
    ORDER BY c.created_at DESC
    LIMIT 10
")->fetchAll();

$page_title = "Client Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section" style="background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%); color: white; padding: 40px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(99,102,241,0.2); border: none;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="margin: 0; font-size: 28px; color: #fff;">Register via Serial Number</h1>
                <p style="margin-top: 10px; opacity: 0.9; font-size: 16px;">Already have your product? Enter the serial number to instantly activate your warranty.</p>
            </div>
            <a href="check_warranty.php" class="btn" style="background: white; color: var(--primary); font-weight: bold; padding: 15px 30px; border-radius: 8px; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <i class="fas fa-search"></i> Check My Serial Number
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-shield-alt icon"></i>
            <h3>Active Warranties</h3>
            <div class="value"><?php echo htmlspecialchars($stats['active_warranties'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-hourglass-end icon"></i>
            <h3>Expired Warranties</h3>
            <div class="value"><?php echo htmlspecialchars($stats['expired_warranties'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock icon"></i>
            <h3>Pending Claims</h3>
            <div class="value"><?php echo htmlspecialchars($stats['pending_claims'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle icon"></i>
            <h3>Completed Claims</h3>
            <div class="value"><?php echo htmlspecialchars($stats['completed_claims'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-file-contract"></i> Recent Warranties</h2>
        <table>
            <thead>
                <tr>
                    <th>Warranty #</th>
                    <th>Product</th>
                    <th>Model</th>
                    <th>Purchase Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($recent_warranties) > 0): ?>
                    <?php foreach($recent_warranties as $warranty): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($warranty['warranty_number']); ?></td>
                            <td><?php echo htmlspecialchars($warranty['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($warranty['model_number']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($warranty['purchase_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($warranty['warranty_end_date'])); ?></td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($warranty['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($warranty['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_warranty.php?id=<?php echo htmlspecialchars($warranty['id']); ?>" class="btn btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            No warranties found. <a href="check_warranty.php" class="btn btn-sm">Check Your Serial #</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-exclamation-triangle"></i> Recent Claims</h2>
        <table>
            <thead>
                <tr>
                    <th>Claim #</th>
                    <th>Product</th>
                    <th>Issue</th>
                    <th>Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($recent_claims) > 0): ?>
                    <?php foreach($recent_claims as $claim): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($claim['claim_number']); ?></td>
                            <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($claim['issue_description'], 0, 30)); ?>...</td>
                            <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                            <td>
                                <span class="priority priority-<?php echo htmlspecialchars($claim['priority']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($claim['priority'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($claim['status']); ?>">
                                    <?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($claim['status']))); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_claim.php?id=<?php echo htmlspecialchars($claim['id']); ?>" class="btn btn-sm" title="View Details"><i class="fas fa-eye"></i></a>
                                <?php if(isset($claim['resolution_type']) && $claim['resolution_type'] == 'refund'): ?>
                                    <a href="view_resolution_statement.php?id=<?php echo $claim['id']; ?>" class="btn btn-sm" style="background: var(--success);" title="View Refund Statement"><i class="fas fa-file-invoice-dollar"></i> Statement</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            No claims filed yet. <a href="file_claim.php">File a claim</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
