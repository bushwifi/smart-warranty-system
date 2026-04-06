<?php
// owner/resolution_approvals.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Handle Authorization Decisions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_resolution'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $claim_id = (int)$_POST['claim_id'];
        $decision = $_POST['process_resolution']; // 'approve' or 'deny'
        $type = $_POST['resolution_type']; // 'repair', 'replacement', 'refund'
        $notes = trim($_POST['notes']);

        $pdo->beginTransaction();
        try {
            if ($decision == 'approve') {
                // 1. Move status based on type
                $new_status = 'completed'; // All final resolutions move to 'completed'
                if ($type == 'repair') {
                    $new_status = 'in_progress'; 
                }
                
                $pdo->prepare("UPDATE claims SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$new_status, $claim_id]);

                // 2. Void warranty for fulfillment & Calculate refund if required
                $refund_amount = 0;
                if ($type == 'refund' || $type == 'replacement') {
                    $stmt = $pdo->prepare("
                        SELECT wr.id, wr.purchase_price, wr.quantity, p.price as base_price 
                        FROM warranty_registrations wr 
                        JOIN claims c ON wr.id = c.warranty_id 
                        JOIN products p ON wr.product_id = p.id
                        WHERE c.id = ?
                    ");
                    $stmt->execute([$claim_id]);
                    $row = $stmt->fetch();
                    
                    if ($type == 'refund') {
                        // Fallback to product base price if purchase_price is 0
                        $unit_price = ($row['purchase_price'] > 0) ? $row['purchase_price'] : $row['base_price'];
                        $refund_amount = $unit_price * $row['quantity'];
                    }
                    
                    // Owner's final decision voids the old coverage
                    $pdo->prepare("UPDATE warranty_registrations SET status = 'voided', warranty_end_date = CURRENT_DATE() WHERE id = ?")->execute([$row['id']]);
                }

                // 3. Record in claim_resolution
                $pdo->prepare("
                    INSERT INTO claim_resolution (claim_id, repaired_by, resolution_type, refund_amount, is_authorized, resolution_notes)
                    VALUES (?, ?, ?, ?, 1, ?)
                ")->execute([$claim_id, $owner_id, $type, $refund_amount, $notes]);

                $message = "Resolution '{$type}' authorized and finalized successfully!";
                logActivity($owner_id, "Authorized and Finalized {$type} for claim #$claim_id");
            } else {
                // Final Denial
                $pdo->prepare("UPDATE claims SET status = 'rejected', updated_at = NOW() WHERE id = ?")->execute([$claim_id]);
                
                // Record the denial reason in claim_resolution for client transparency
                $pdo->prepare("
                    INSERT INTO claim_resolution (claim_id, repaired_by, resolution_type, refund_amount, is_authorized, resolution_notes)
                    VALUES (?, ?, 'rejected', 0, 0, ?)
                ")->execute([$claim_id, $owner_id, $notes]);

                logActivity($owner_id, "Denied {$type} for claim #$claim_id");
                $message = "Resolution request denied. Claim marked as Rejected.";
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error processing resolution: " . $e->getMessage();
        }
    }
}

// Get Pending resolutions (All resolutions that are not yet authorized/completed)
// Technically where claims.status matches the recommendation action status
$pending_resolutions = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, p.price as base_price, wr.serial_number, wr.purchase_price, wr.quantity,
           cv.findings as tech_findings, cv.recommended_action as type, cv.verification_notes as tech_notes, t.full_name as tech_name
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    JOIN claim_verification cv ON c.id = cv.claim_id
    JOIN users t ON cv.technician_id = t.id
    WHERE (c.status = 'pending_refund' OR (c.status = 'approved' AND c.id NOT IN (SELECT claim_id FROM claim_resolution)))
    ORDER BY c.created_at ASC
")->fetchAll();

$page_title = "Resolution Approvals";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-tasks"></i> Resolution Authorization Portal</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Review and authorize technician evaluations before final fulfillment.</p>

        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <?php if(count($pending_resolutions) > 0): ?>
            <?php foreach($pending_resolutions as $res): 
                $fraud = analyzeClaimForFraud($res['id']);
                $type_label = strtoupper($res['type']);
                $unit_price = ($res['purchase_price'] > 0) ? $res['purchase_price'] : $res['base_price'];
                $total_value = $unit_price * $res['quantity'];
            ?>
                <div class="stat-card" style="margin-bottom: 25px; border-left: 5px solid var(--primary); padding: 0;">
                    <div style="display: flex; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 300px; padding: 25px; border-right: 1px solid #eee;">
                            <div class="badge <?php echo $res['type'] == 'refund' ? 'bg-danger' : 'bg-info'; ?>" style="margin-bottom: 15px; font-size: 14px;">RECOMMENDED: <?php echo $type_label; ?></div>
                            <h3>Claim #<?php echo htmlspecialchars($res['claim_number']); ?></h3>
                            <p><strong>Customer:</strong> <?php echo htmlspecialchars($res['customer_name']); ?></p>
                            <p><strong>Product:</strong> <?php echo htmlspecialchars($res['product_name']); ?> (<code><?php echo $res['serial_number']; ?></code>)</p>
                            
                            <?php if($res['type'] == 'refund'): ?>
                                <p><strong>Refund Amount:</strong> <span style="font-size: 18px; font-weight: bold; color: #dc3545;"><?php echo CURRENCY; ?><?php echo number_format($total_value, 2); ?></span></p>
                            <?php endif; ?>
                            
                            <hr style="margin: 15px 0; border-top: 1px dashed #ddd;">
                            <div style="padding: 10px; background: #f1f5f9; border-radius: 5px;">
                                <strong>Technical Eval (<?php echo htmlspecialchars($res['tech_name']); ?>):</strong><br>
                                <small><?php echo nl2br(htmlspecialchars($res['tech_findings'])); ?></small>
                            </div>
                        </div>

                        <div style="flex: 1; min-width: 300px; padding: 25px; background: #fafafa;">
                            <h4><i class="fas fa-shield-alt"></i> Fraud & Risk Score</h4>
                            <div style="margin: 15px 0;">
                                <?php echo getFraudRiskBadge($fraud['score']); ?>
                                <?php if(!empty($fraud['flags'])): ?>
                                    <ul style="font-size: 11px; color: #dc3545; padding-left: 15px;">
                                        <?php foreach($fraud['flags'] as $flag): ?><li><?php echo $flag; ?></li><?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="claim_id" value="<?php echo $res['id']; ?>">
                                <input type="hidden" name="resolution_type" value="<?php echo $res['type']; ?>">
                                <div class="form-group">
                                    <label>Authorization Notes</label>
                                    <textarea name="notes" placeholder="Final comments for the client..." required style="min-height: 80px;"></textarea>
                                </div>
                                <div style="display: flex; gap: 10px; margin-top: 10px;">
                                    <button type="submit" name="process_resolution" value="approve" class="btn" style="flex:1; background: #28a745;">Authorize Decision</button>
                                    <button type="submit" name="process_resolution" value="deny" class="btn" style="flex:1; background: #dc3545;">Reject Decision</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-check" style="color: #cbd5e1; font-size: 3rem;"></i>
                <p>No resolutions currently pending your authorization.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
