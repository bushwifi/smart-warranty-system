<?php
// technician/dashboard.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'technician') {
    header("Location: ../index.php");
    exit();
}

$technician_id = (int)$_SESSION['user_id'];

// Get statistics
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM claims WHERE assigned_technician_id = $technician_id AND status IN ('pending', 'under_review')) as pending_claims,
        (SELECT COUNT(*) FROM claims WHERE assigned_technician_id = $technician_id AND status = 'approved') as approved_claims,
        (SELECT COUNT(*) FROM claims WHERE assigned_technician_id = $technician_id AND status = 'rejected') as rejected_claims,
        (SELECT COUNT(*) FROM claim_verification WHERE technician_id = $technician_id) as total_verified,
        (SELECT COUNT(*) FROM claim_resolution WHERE repaired_by = $technician_id) as total_repaired
")->fetch();

// Get pending claims
$pending_claims = $pdo->query("
    SELECT c.*, u.full_name as customer_name, u.email, u.phone,
           p.product_name, p.model_number, wr.serial_number
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE c.assigned_technician_id = $technician_id AND c.status IN ('pending', 'under_review')
    ORDER BY 
        CASE c.priority 
            WHEN 'urgent' THEN 1 
            WHEN 'high' THEN 2 
            WHEN 'medium' THEN 3 
            ELSE 4 
        END,
        c.created_at ASC
    LIMIT 10
")->fetchAll();

// Get recent verifications
$recent_verifications = $pdo->query("
    SELECT cv.*, c.claim_number, u.full_name as customer_name,
           p.product_name, c.status as claim_status
    FROM claim_verification cv
    JOIN claims c ON cv.claim_id = c.id
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE cv.technician_id = $technician_id
    ORDER BY cv.verification_date DESC
    LIMIT 10
")->fetchAll();

// Get approved claims (Waiting to start repair or replacement)
$approved_claims_list = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, cv.recommended_action
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    LEFT JOIN claim_verification cv ON c.id = cv.claim_id
    WHERE c.assigned_technician_id = $technician_id AND c.status = 'approved'
    ORDER BY c.updated_at DESC
")->fetchAll();

// Handle Start Repair action
if (isset($_GET['action']) && $_GET['action'] == 'start_repair' && isset($_GET['id'])) {
    $cid = (int)$_GET['id'];
    $pdo->prepare("UPDATE claims SET status = 'in_progress', updated_at = NOW() WHERE id = ? AND status = 'approved'")->execute([$cid]);
    logActivity($technician_id, "Started repair for claim #$cid");
    header("Location: dashboard.php?msg=repair_started");
    exit();
}

// Get in-progress claims
$in_progress = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name,
           cr.resolution_type, cr.notes
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    LEFT JOIN claim_resolution cr ON c.id = cr.claim_id
    WHERE c.assigned_technician_id = $technician_id AND c.status = 'in_progress'
    ORDER BY c.updated_at DESC
    LIMIT 5
")->fetchAll();

$page_title = "Technician Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <h3>Pending Claims</h3>
            <div class="stat-value"><?php echo htmlspecialchars($stats['pending_claims'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <h3>Approved Claims</h3>
            <div class="stat-value"><?php echo htmlspecialchars($stats['approved_claims'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <h3>Rejected Claims</h3>
            <div class="stat-value"><?php echo htmlspecialchars($stats['rejected_claims'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <h3>Total Verified</h3>
            <div class="stat-value"><?php echo htmlspecialchars($stats['total_verified'] ?? 0); ?></div>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-clock"></i> Claims Pending Verification</h2>
        <?php if(count($pending_claims) > 0): ?>
            <div style="overflow-x: auto;">
                 <table>
                    <thead>
                         <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Serial #</th>
                            <th>Issue Category</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Action</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_claims as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['serial_number']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($claim['issue_category'])); ?></td>
                                <td>
                                    <span class="priority priority-<?php echo htmlspecialchars($claim['priority']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($claim['priority'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                                <td>
                                    <a href="verify_claim.php?id=<?php echo htmlspecialchars($claim['id']); ?>" class="btn btn-sm">
                                        <i class="fas fa-search"></i> Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                <p>No pending claims to verify. Great job!</p>
            </div>
        <?php endif; ?>
    <div class="section">
        <h2><i class="fas fa-thumbs-up"></i> Approved Claims (Ready for Action)</h2>
        <?php if(count($approved_claims_list) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Recommended Action</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($approved_claims_list as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst($claim['recommended_action'])); ?></span>
                                </td>
                                <td>
                                    <?php if($claim['recommended_action'] == 'repair'): ?>
                                        <a href="dashboard.php?action=start_repair&id=<?php echo $claim['id']; ?>" class="btn btn-sm" style="background:#007bff;">
                                            <i class="fas fa-tools"></i> Start Repair
                                        </a>
                                    <?php elseif($claim['recommended_action'] == 'replace'): ?>
                                        <a href="complete_repair.php?id=<?php echo $claim['id']; ?>" class="btn btn-sm" style="background:#6f42c1;">
                                            <i class="fas fa-exchange-alt"></i> Process Replacement
                                        </a>
                                    <?php else: ?>
                                        <a href="complete_repair.php?id=<?php echo $claim['id']; ?>" class="btn btn-sm" style="background:#28a745;">
                                            <i class="fas fa-check-circle"></i> Resolve
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-info-circle"></i>
                <p>No approved claims waiting for action.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-wrench"></i> Claims In Progress</h2>
        <?php if(count($in_progress) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Resolution Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($in_progress as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($claim['resolution_type'] ?? 'Pending')); ?></td>
                                <td><span class="status status-in_progress">In Progress</span></td>
                                <td>
                                    <a href="complete_repair.php?id=<?php echo htmlspecialchars($claim['id']); ?>" class="btn btn-sm" style="background:#28a745;">
                                        <i class="fas fa-check-double"></i> Complete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check"></i>
                <p>No claims in progress.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-history"></i> My Recent Verifications</h2>
        <?php if(count($recent_verifications) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Valid</th>
                            <th>Action</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_verifications as $ver): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ver['claim_number']); ?></td>
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
                                <td><?php echo date('M d, Y', strtotime($ver['verification_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>No verifications yet. Start reviewing pending claims!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
