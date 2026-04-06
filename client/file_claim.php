<?php
// client/file_claim.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$message = '';
$error = '';

// Get user's active warranties
$warranties = $pdo->query("
    SELECT wr.*, p.product_name, p.model_number, p.brand 
    FROM warranty_registrations wr
    JOIN products p ON wr.product_id = p.id
    WHERE wr.user_id = $user_id 
    AND wr.warranty_end_date >= CURDATE()
    ORDER BY wr.warranty_end_date ASC
")->fetchAll();

// Get any current active claims (to block doubles)
$active_claims_per_warranty = $pdo->query("
    SELECT warranty_id, claim_number, status 
    FROM claims 
    WHERE user_id = $user_id 
    AND status NOT IN ('completed', 'rejected')
")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_claim'])) {
    $warranty_id = (int) $_POST['warranty_id'];
    $issue_description = $_POST['issue_description'];
    $issue_category = $_POST['issue_category'];
    $priority = $_POST['priority'];
    
    // Fetch warranty number first for validation
    $stmt = $pdo->prepare("SELECT warranty_number FROM warranty_registrations WHERE id = ? AND user_id = ?");
    $stmt->execute([$warranty_id, $user_id]);
    $w_row = $stmt->fetch();
    
    if ($w_row) {
        $validation = validateWarranty($w_row['warranty_number']);
        
        if ($validation['valid']) {
            // CHECK FOR ACTIVE CLAIMS
            $check_stmt = $pdo->prepare("SELECT id FROM claims WHERE warranty_id = ? AND status NOT IN ('completed', 'rejected')");
            $check_stmt->execute([$warranty_id]);
            if ($check_stmt->fetch()) {
                $error = "You already have an active claim for this product. Please wait for the current claim to be resolved before filing a new one.";
            } else {
                $claim_number = generateClaimNumber();
                
                $insert = $pdo->prepare("
                    INSERT INTO claims (claim_number, warranty_id, user_id, issue_description, issue_category, priority) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                if ($insert->execute([$claim_number, $warranty_id, $user_id, $issue_description, $issue_category, $priority])) {
                    $claim_id = $pdo->lastInsertId();
                    
                    // Notifications and Logging
                    $notify_msg = "New claim #$claim_number filed for " . $validation['data']['product_name'];
                    $techs = $pdo->query("SELECT id FROM users WHERE user_type IN ('admin', 'technician')")->fetchAll();
                    foreach($techs as $tech) {
                        createNotification($tech['id'], 'New Claim Filed', $notify_msg, 'info');
                    }
                    
                    createNotification($user_id, 'Claim Submitted', "Claim #$claim_number submitted successfully.", 'success');
                    logActivity($user_id, "Filed claim #$claim_number");
                    
                    $message = "Claim filed successfully! Claim #: $claim_number";
                } else {
                    $error = "Failed to submit claim to database.";
                }
            }
        } else {
            $error = "Warranty Validation Failed: " . $validation['error'];
        }
    } else {
        $error = "Invalid warranty selection or unauthorized access.";
    }
}

$page_title = "File Claim";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="form-container">
        <h2><i class="fas fa-file-medical"></i> File a Warranty Claim</h2>
        
        <?php if($message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <strong>Important:</strong> Before filing a claim, please ensure your product is within the warranty period and the issue is covered under warranty terms.
        </div>
        
        <?php if(count($warranties) == 0): ?>
            <div class="message error">
                <i class="fas fa-exclamation-triangle"></i> You don't have any active warranties linked to your profile. 
                <a href="check_warranty.php" style="color: #721c24; text-decoration: underline; font-weight: bold;">Verify your serial number here</a> first.
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Select Product with Active Warranty</label>
                    <select name="warranty_id" required onchange="checkActiveClaim(this)">
                        <option value="">Choose a product...</option>
                        <?php foreach($warranties as $warranty): ?>
                            <?php 
                                $has_active = isset($active_claims_per_warranty[$warranty['id']]); 
                                $status_text = $has_active ? " [ACTIVE CLAIM: " . strtoupper($active_claims_per_warranty[$warranty['id']]['status']) . "]" : "";
                            ?>
                            <option value="<?php echo htmlspecialchars($warranty['id']); ?>" <?php echo $has_active ? 'disabled' : ''; ?>>
                                <?php echo htmlspecialchars($warranty['product_name']); ?> (SN: <?php echo htmlspecialchars($warranty['serial_number']); ?>)
                                <?php echo $status_text; ?>
                                — Valid till: <?php echo date('M d, Y', strtotime($warranty['warranty_end_date'])); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Professional Policy Summary -->
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin: 25px 0;">
                    <h4 style="margin: 0 0 10px 0; color: var(--primary); font-size: 14px;"><i class="fas fa-shield-alt"></i> Coverage Policy Summary</h4>
                    <ul style="margin:0; padding-left: 20px; font-size: 13px; color: #64748b; line-height: 1.6;">
                        <li><strong>Repair Claims:</strong> You can file unlimited repair requests during your warranty period.</li>
                        <li><strong>Replacement/Refund:</strong> Acceptance of a replacement or refund voids the original coverage.</li>
                        <li><strong>Concurrency:</strong> For professional processing, you can only have <strong>one active claim</strong> per product at a time.</li>
                    </ul>
                </div>
                
                <div class="form-group">
                    <label>Issue Category</label>
                    <select name="issue_category" required>
                        <option value="">Select category...</option>
                        <option value="hardware">Hardware Issue</option>
                        <option value="software">Software Issue</option>
                        <option value="performance">Performance Issue</option>
                        <option value="physical">Physical Damage</option>
                        <option value="accessories">Accessories Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Priority Level</label>
                    <select name="priority" required>
                        <option value="low">Low - Minor issue, can wait</option>
                        <option value="medium" selected>Medium - Normal priority</option>
                        <option value="high">High - Product unusable</option>
                        <option value="urgent">Urgent - Safety concern / Critical</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Describe the Issue in Detail</label>
                    <textarea name="issue_description" required 
                        placeholder="Please describe the problem you're experiencing. Include:
- When did the issue start?
- What were you doing when it happened?
- Any error messages?
- Steps to reproduce the issue"></textarea>
                </div>
                
                <button type="submit" name="submit_claim" class="btn">
                    <i class="fas fa-paper-plane"></i> Submit Claim
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
