<?php
// technician/verify_claim.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'technician') {
    header("Location: ../index.php");
    exit();
}

$technician_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$claim_id = (int)$_GET['id'];

// Fetch Claim
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as customer_name, u.email, u.phone, u.address,
           wr.serial_number, wr.purchase_date, wr.warranty_start_date, wr.warranty_end_date, wr.purchase_receipt, wr.purchase_price, wr.quantity,
           p.product_name, p.model_number, p.brand, p.warranty_terms
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    WHERE c.id = ?
");
$stmt->execute([$claim_id]);
$claim = $stmt->fetch();

if (!$claim) {
    header("Location: dashboard.php");
    exit();
}

// Automatically mark as under_review if pending
if ($claim['status'] == 'pending') {
    $pdo->prepare("UPDATE claims SET status = 'under_review' WHERE id = ?")->execute([$claim_id]);
    $claim['status'] = 'under_review';
    logActivity($technician_id, "Started review of claim #{$claim['claim_number']}");
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_claim'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $findings = trim($_POST['findings']);
        $is_valid = isset($_POST['is_valid']) ? 1 : 0;
        $recommended_action = $_POST['recommended_action'];
        $verification_notes = trim($_POST['verification_notes']);
        
        // Check if verification already exists
        $checkStmt = $pdo->prepare("SELECT id FROM claim_verification WHERE claim_id = ?");
        $checkStmt->execute([$claim_id]);
        
        if ($checkStmt->fetch()) {
            $updateStmt = $pdo->prepare("
                UPDATE claim_verification 
                SET findings = ?, is_valid = ?, recommended_action = ?, verification_notes = ?
                WHERE claim_id = ?
            ");
            $success = $updateStmt->execute([$findings, $is_valid, $recommended_action, $verification_notes, $claim_id]);
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO claim_verification (claim_id, technician_id, findings, is_valid, recommended_action, verification_notes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $success = $insertStmt->execute([$claim_id, $technician_id, $findings, $is_valid, $recommended_action, $verification_notes]);
        }
        
        if ($success) {
            $new_status = $is_valid ? 'approved' : 'rejected';
            
            // SPECIAL RULE: If it's a refund, it MUST go to 'pending_refund' for Owner Approval
            if ($is_valid && $recommended_action == 'refund') {
                $new_status = 'pending_refund';
            }
            
            // If it's a replacement or refund, we might mark it as such immediately, 
            // but the user wants "Approved" as the primary decision.
            // However, Replacement/Refund are terminal "Resolutions".
            // Let's stick to Approved/Rejected first.
            
            $statusStmt = $pdo->prepare("UPDATE claims SET status = ?, updated_at = NOW() WHERE id = ?");
            $statusStmt->execute([$new_status, $claim_id]);
            
            // Log decision in Activity Logs
            logActivity($technician_id, "Decision made for claim #{$claim['claim_number']}: " . strtoupper($new_status));
            
            // Create notification for customer
            createNotification($claim['user_id'], 'Claim Decision', "Your claim #{$claim['claim_number']} has been " . ($is_valid ? 'Approved' : 'Rejected') . ". Reason: " . ($is_valid ? 'Valid warranty coverage' : 'Issue not covered'), $is_valid ? 'success' : 'danger');
            
            $message = "Claim marked as " . strtoupper($new_status) . " successfully!";
            header("refresh:2;url=dashboard.php");
        } else {
            $error = "Error saving verification.";
        }
    }
}

$fraud_analysis = analyzeClaimForFraud($claim_id);

$page_title = "Verify Claim";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>
<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-search"></i> Verify Claim: <?php echo htmlspecialchars($claim['claim_number']); ?></h2>
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex:1; min-width: 300px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <h3>Claim Info</h3>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($claim['customer_name']); ?></p>
                <p><strong>Product:</strong> <?php echo htmlspecialchars($claim['product_name']); ?></p>
                <p><strong>Action Date:</strong> <?php echo date('M d, Y', strtotime($claim['created_at'])); ?></p>
                <hr style="margin: 10px 0;">
                <p><strong>Issue:</strong><br><?php echo nl2br(htmlspecialchars($claim['issue_description'])); ?></p>
                
                <div style="margin-top: 20px; padding: 15px; background: #fff; border-radius: 8px; border: 1px solid #dee2e6;">
                    <h4><i class="fas fa-shield-alt"></i> Fraud Risk Analysis</h4>
                    <div style="margin: 10px 0;"><?php echo getFraudRiskBadge($fraud_analysis['score']); ?></div>
                    <?php if(!empty($fraud_analysis['flags'])): ?>
                        <ul style="color: #dc3545; font-size: 13px; padding-left: 20px;">
                            <?php foreach($fraud_analysis['flags'] as $flag): ?>
                                <li><?php echo htmlspecialchars($flag); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <?php if($claim['purchase_receipt']): ?>
                <div style="margin-top: 20px;">
                    <h4><i class="fas fa-file-invoice"></i> Purchase Receipt</h4>
                    <a href="../<?php echo htmlspecialchars($claim['purchase_receipt']); ?>" target="_blank">
                        <?php if(pathinfo($claim['purchase_receipt'], PATHINFO_EXTENSION) == 'pdf'): ?>
                            <div style="padding: 20px; background: #eee; text-align: center; border-radius: 5px;">
                                <i class="fas fa-file-pdf" style="font-size: 40px; color: #dc3545;"></i><br>View PDF Receipt
                            </div>
                        <?php else: ?>
                            <img src="../<?php echo htmlspecialchars($claim['purchase_receipt']); ?>" style="max-width: 100%; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <?php endif; ?>
                    </a>
                    <div style="margin-top:10px; font-size:13px; color:#666;">
                        <i class="fas fa-barcode"></i> Serial: <strong><?php echo htmlspecialchars($claim['serial_number']); ?></strong>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div style="flex:2; min-width: 300px;">
                <form method="POST" action="" class="form-container" style="max-width: 100%; margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <h3>Submit Verification</h3>
                    
                    <div class="form-group" style="margin-top:15px;">
                        <label>Technical Findings</label>
                        <textarea name="findings" rows="4" required placeholder="Describe what you found after evaluating the device."></textarea>
                    </div>
                    
                    <div class="form-group" style="margin: 20px 0;">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:16px;">
                            <input type="checkbox" name="is_valid" value="1" style="width:20px; height:20px;">
                            <strong>Issue is valid under warranty terms</strong>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>Decision / Recommended Action</label>
                        <select name="recommended_action" required>
                            <option value="">Select Action...</option>
                            <option value="repair">Approve for Repair</option>
                            <option value="replace">Recommend Replacement</option>
                            <option value="refund">Recommend Refund</option>
                            <option value="reject">Reject Claim (Not Covered)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Internal Verification Notes (Hidden from Client)</label>
                        <textarea name="verification_notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="verify_claim" class="btn" style="background:#28a745;">Submit Verification</button>
                    <a href="dashboard.php" class="btn" style="background:#6c757d; text-align:center;">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
