<?php
// owner/assign_technician.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Handle Technician Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_tech'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $claim_id = (int)$_POST['claim_id'];
        $tech_id = (int)$_POST['technician_id'];
        
        $pdo->beginTransaction();
        try {
            // Update the claim with the assigned technician
            $stmt = $pdo->prepare("UPDATE claims SET assigned_technician_id = ?, status = 'under_review', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$tech_id, $claim_id]);
            
            // Notify the technician
            $tech_info = $pdo->query("SELECT full_name FROM users WHERE id = $tech_id")->fetch();
            createNotification($tech_id, 'New Claim Assigned', "You have been assigned to claim #$claim_id. Please evaluate it as soon as possible.", 'info');
            
            logActivity($owner_id, "Assigned claim #$claim_id to technician: {$tech_info['full_name']}");
            
            $pdo->commit();
            $message = "Technician assigned successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error assigning technician: " . $e->getMessage();
        }
    }
}

// Get all Technicians
$technicians = $pdo->query("SELECT id, full_name, username FROM users WHERE user_type = 'technician' AND status = 'active' ORDER BY full_name")->fetchAll();

// Get Pending Claims (Those needing assignment)
$pending_claims = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, wr.serial_number
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE (c.assigned_technician_id IS NULL OR c.status = 'pending')
    ORDER BY c.created_at ASC
")->fetchAll();

$page_title = "Assign Technicians";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-user-plus"></i> Manual Claim Assignment</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Use this interface to assign incoming claims to technical staff for evaluation.</p>
        
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <?php if(count($pending_claims) > 0): ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Claim #</th>
                            <th>Customer</th>
                            <th>Product / Serial</th>
                            <th>Current Status</th>
                            <th>Assign To</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_claims as $claim): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong><br><small><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></small></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?><br><code><?php echo htmlspecialchars($claim['serial_number']); ?></code></td>
                                <td><span class="badge bg-warning"><?php echo ucfirst($claim['status']); ?></span></td>
                                <form method="POST" action="">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="claim_id" value="<?php echo $claim['id']; ?>">
                                    <td>
                                        <select name="technician_id" required style="width: auto; padding: 5px; height: 35px;">
                                            <option value="">Select Tech...</option>
                                            <?php foreach($technicians as $tech): ?>
                                                <option value="<?php echo $tech['id']; ?>"><?php echo htmlspecialchars($tech['full_name']); ?> (@<?php echo $tech['username']; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="assign_tech" class="btn btn-sm">Assign</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-double" style="color: var(--success); font-size: 3rem;"></i>
                <p>All claims have been successfully assigned to technicians.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
