<?php
// owner/refund_approvals.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Handle Approval/Denial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_refund'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $claim_id = (int)$_POST['claim_id'];
        $decision = $_POST['process_refund']; // Fixed: was looking for 'decision' which didn't exist in form
        $notes = trim($_POST['notes']);

        $pdo->beginTransaction();
        try {
            if ($decision == 'approve') {
                // 1. Update Claim Status
                $pdo->prepare("UPDATE claims SET status = 'completed', updated_at = NOW() WHERE id = ?")->execute([$claim_id]);
                
                // 2. Fetch refund amount from registration
                $stmt = $pdo->prepare("
                    SELECT wr.purchase_price, wr.quantity, c.user_id 
                    FROM claims c 
                    JOIN warranty_registrations wr ON c.warranty_id = wr.id 
                    WHERE c.id = ?
                ");
                $stmt->execute([$claim_id]);
                $claim_data = $stmt->fetch();
                $total_refund = $claim_data['purchase_price'] * $claim_data['quantity'];

                // 3. Record in Resolution
                $pdo->prepare("
                    INSERT INTO claim_resolution (claim_id, repaired_by, resolution_type, refund_amount, is_authorized, resolution_notes)
                    VALUES (?, ?, 'refund', ?, 1, ?)
                ")->execute([$claim_id, $owner_id, $total_refund, $notes]);

                // 4. Notify Customer
                createNotification($claim_data['user_id'], 'Refund Authorized', "Your refund of $" . number_format($total_refund, 2) . " for claim #$claim_id has been authorized.", 'success');
                logActivity($owner_id, "Authorized refund for claim #$claim_id ($" . number_format($total_refund, 2) . ")");
                
                $message = "Refund authorized successfully!";
            } else {
                // Deny Refund
                $pdo->prepare("UPDATE claims SET status = 'approved', updated_at = NOW() WHERE id = ?")->execute([$claim_id]);
                logActivity($owner_id, "Denied refund for claim #$claim_id. Moved back to approved.");
                $message = "Refund request denied. Claim moved back to 'Approved' for alternative resolution.";
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error processing refund: " . $e->getMessage();
        }
    }
}

// Get Pending Refunds
$pending_refunds = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, wr.purchase_receipt, wr.purchase_price, wr.quantity,
           cv.findings as tech_findings, cv.verification_notes as tech_notes, t.full_name as tech_name
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    JOIN claim_verification cv ON c.id = cv.claim_id
    JOIN users t ON cv.technician_id = t.id
    WHERE c.status = 'pending_refund'
    ORDER BY c.created_at ASC
")->fetchAll();

$page_title = "Refund Approvals";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-hand-holding-usd"></i> Pending Refund Approvals</h2>
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <?php if(count($pending_refunds) > 0): ?>
            <?php foreach($pending_refunds as $refund): 
                $fraud = analyzeClaimForFraud($refund['id']);
                $total_value = $refund['purchase_price'] * $refund['quantity'];
            ?>
                <div class="stat-card" style="margin-bottom: 25px; border-left: 5px solid var(--warning); padding: 0;">
                    <div style="display: flex; flex-wrap: wrap;">
                        <!-- Left Pillar: Info -->
                        <div style="flex: 1; min-width: 300px; padding: 25px; border-right: 1px solid #eee;">
                            <h3>Claim #<?php echo htmlspecialchars($refund['claim_number']); ?></h3>
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($refund['customer_name']); ?></p>
                            <p><strong>Product:</strong> <?php echo htmlspecialchars($refund['product_name']); ?></p>
                            <p><strong>Total Refund Value:</strong> <span style="font-size: 20px; color: #dc3545; font-weight: bold;"><?php echo CURRENCY; ?><?php echo number_format($total_value, 2); ?></span> (<?php echo $refund['quantity']; ?> unit@ <?php echo CURRENCY; ?><?php echo number_format($refund['purchase_price'], 2); ?>)</p>
                            
                            <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                <strong>Technician Findings (<?php echo htmlspecialchars($refund['tech_name']); ?>):</strong><br>
                                <small><?php echo nl2br(htmlspecialchars($refund['tech_findings'])); ?></small>
                            </div>
                        </div>

                        <!-- Right Pillar: Verification & Action -->
                        <div style="flex: 1; min-width: 300px; padding: 25px; background: #fafafa;">
                            <h4><i class="fas fa-shield-alt"></i> Security & Verification</h4>
                            <div style="margin: 10px 0;">
                                <?php echo getFraudRiskBadge($fraud['score']); ?>
                                <?php if(!empty($fraud['flags'])): ?>
                                    <ul style="font-size: 11px; color: #dc3545; margin-top: 5px;">
                                        <?php foreach($fraud['flags'] as $flag): ?><li><?php echo $flag; ?></li><?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>

                            <?php if($refund['purchase_receipt']): ?>
                                <a href="../<?php echo htmlspecialchars($refund['purchase_receipt']); ?>" target="_blank" style="display: block; margin: 15px 0;">
                                    <i class="fas fa-file-invoice"></i> View Purchase Receipt
                                </a>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="claim_id" value="<?php echo $refund['id']; ?>">
                                <div class="form-group">
                                    <label>Owner's Review Notes</label>
                                    <textarea name="notes" placeholder="Reasons for approval/denial..." style="min-height: 80px;"></textarea>
                                </div>
                                <div style="display: flex; gap: 10px; margin-top: 10px;">
                                    <button type="submit" name="process_refund" value="approve" class="btn" style="flex:1; background: #28a745;">Authorize Refund</button>
                                    <button type="submit" name="process_refund" value="deny" class="btn" style="flex:1; background: #6c757d;">Deny Request</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                <p>No refund requests pending your authorization.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
