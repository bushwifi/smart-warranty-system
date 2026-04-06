<?php
// technician/pending_claims.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'technician') {
    header("Location: " . SITE_URL . "index.php");
    exit();
}

// Get all pending claims
$claims = $pdo->query("
    SELECT c.*, u.full_name as customer_name, u.email, u.phone,
           p.product_name, p.model_number, wr.serial_number
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE c.status IN ('pending', 'under_review')
    ORDER BY 
        CASE c.priority 
            WHEN 'urgent' THEN 1 
            WHEN 'high' THEN 2 
            WHEN 'medium' THEN 3 
            ELSE 4 
        END,
        c.created_at ASC
")->fetchAll();

$page_title = "Pending Claims";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-clock"></i> Pending Claims for Verification</h2>
        <?php if(count($claims) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Serial #</th>
                            <th>Issue</th>
                            <th>Priority</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($claims as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['serial_number']); ?></td>
                                <td><?php echo htmlspecialchars(substr($claim['issue_description'], 0, 40)); ?>...</td>
                                <td><span class="priority priority-<?php echo htmlspecialchars($claim['priority']); ?>"><?php echo ucfirst(htmlspecialchars($claim['priority'])); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                                <td><a href="verify_claim.php?id=<?php echo htmlspecialchars($claim['id']); ?>" class="btn">Review</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                <p>No pending claims to verify!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
