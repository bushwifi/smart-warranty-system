<?php
// client/view_claim.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client' || !isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$claim_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, p.product_name, p.model_number, p.brand, wr.warranty_number, wr.serial_number,
           wr.purchase_price, wr.quantity,
           cr.resolution_type, cr.resolution_notes, cv.findings, t.full_name as technician_name
    FROM claims c
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    LEFT JOIN claim_verification cv ON c.id = cv.claim_id
    LEFT JOIN claim_resolution cr ON c.id = cr.claim_id
    LEFT JOIN users t ON c.assigned_technician_id = t.id
    WHERE c.id = ? AND c.user_id = ?
");
$stmt->execute([$claim_id, $user_id]);
$claim = $stmt->fetch();

// Ensure it only shows for Refund if specialized
if ($claim && $claim['status'] == 'refunded' && $claim['resolution_type'] != 'refund') {
    // Safety check just in case status is out of sync
}

if (!$claim) {
    header("Location: claims.php");
    exit();
}

$page_title = "View Claim Details";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-file-medical"></i> Claim Details: <?php echo htmlspecialchars($claim['claim_number']); ?></h2>
        
        <div style="margin-top:20px; background:#f8f9fa; padding:20px; border-radius:8px;">
            <p><strong>Status:</strong> 
                <span class="status status-<?php echo htmlspecialchars($claim['status']); ?>"><?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($claim['status']))); ?></span>
            </p>
            <hr style="margin: 15px 0;">
            <p><strong>Product:</strong> <?php echo htmlspecialchars($claim['brand'] . ' ' . $claim['product_name']); ?></p>
            <p><strong>Serial Number:</strong> <?php echo htmlspecialchars($claim['serial_number']); ?></p>
            <p><strong>Warranty Info:</strong> <a href="view_warranty.php?id=<?php echo $claim['warranty_id']; ?>">View Warranty</a></p>
            <hr style="margin: 15px 0;">
            <p><strong>Issue Category:</strong> <?php echo htmlspecialchars(ucfirst($claim['issue_category'])); ?></p>
            <p><strong>Date Filed:</strong> <?php echo date('M d, Y H:i A', strtotime($claim['created_at'])); ?></p>
            <p><strong>Problem Description:</strong><br><span style="white-space:pre-wrap; color:#666;"><?php echo htmlspecialchars($claim['issue_description']); ?></span></p>
        </div>
        
        
        <?php if(isset($claim['resolution_type']) && $claim['resolution_type'] == 'refund'): ?>
        <!-- REFUND STATEMENT BOX (Shows for 'completed' but and type='refund') -->
        <div style="margin-top:40px; border: 2px solid var(--success); border-radius: 12px; overflow: hidden; background: #fff;">
            <div style="background: var(--success); color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin:0; font-size: 20px;"><i class="fas fa-file-invoice-dollar"></i> Management Decision Statement</h3>
                    <p style="margin:5px 0 0 0; opacity: 0.9;">Authorized Final Resolution & Receipt</p>
                </div>
                <button onclick="window.print()" class="btn" style="background: rgba(255,255,255,0.2); border: 1px solid white; color: white;">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
            <div style="padding: 25px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div>
                        <h4 style="color: var(--text-muted); text-transform: uppercase; font-size: 12px; margin-bottom: 10px;">Refund Amount Detailed</h4>
                        <div style="font-size: 32px; font-weight: 800; color: var(--success);"><?php echo CURRENCY; ?><?php echo number_format($claim['purchase_price'] * ($claim['quantity'] ?: 1), 2); ?></div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-top: 5px;">Refunded to Original Method of Payment</p>
                    </div>
                    <div>
                        <h4 style="color: var(--text-muted); text-transform: uppercase; font-size: 12px; margin-bottom: 10px;">Resolution Authority</h4>
                        <div style="font-weight: 600;">Authorized by Management</div>
                        <div style="font-size: 13px; color: var(--text-muted); margin-top: 5px;">Date: <?php echo date('M d, Y', strtotime($claim['updated_at'])); ?></div>
                    </div>
                </div>

                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
                    <h4 style="font-size: 14px; margin-bottom: 15px;"><i class="fas fa-search"></i> Reasons for Decision:</h4>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid var(--success);">
                        <p style="font-style: italic; color: var(--text-main); line-height: 1.6;">"<?php echo nl2br(htmlspecialchars($claim['resolution_notes'])); ?>"</p>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif($claim['status'] == 'rejected'): ?>
        <!-- REJECTED CLAIM BOX -->
        <div style="margin-top:40px; border: 2px solid #dc3545; border-radius: 12px; overflow: hidden; background: #fff;">
            <div style="background: #dc3545; color: white; padding: 15px 25px;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-times-circle"></i> Claim Decision: Rejected</h3>
            </div>
            <div style="padding: 25px;">
                <h4 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Reason for Decision:</h4>
                <div style="padding: 20px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545;">
                    <p style="margin: 0; color: #b91c1c; line-height: 1.6; font-weight: 500;">
                        <?php echo htmlspecialchars($claim['resolution_notes'] ?: 'Your claim has been reviewed and unfortunately rejected as it does not meet our warranty fulfillment criteria.'); ?>
                    </p>
                </div>
                <div style="margin-top: 15px; font-size: 13px; color: #64748b;">
                    <i class="fas fa-info-circle"></i> If you believe this is an error, please contact our support team.
                </div>
            </div>
        </div>

        <?php elseif($claim['status'] == 'completed'): ?>
        <!-- UNIFIED COMPLETED BOX (Repairs/Replacements) -->
        <div style="margin-top:40px; border: 2px solid var(--primary); border-radius: 12px; overflow: hidden; background: #fff;">
            <div style="background: var(--primary); color: white; padding: 15px 25px;">
                <h3 style="margin: 0; font-size: 18px;"><i class="fas fa-check-circle"></i> Claim Resolved Successfully</h3>
            </div>
            <div style="padding: 25px;">
                <div style="margin-bottom: 20px; font-weight: bold; color: var(--primary); text-transform: uppercase; font-size: 13px;">
                    Resolution Outcome: <?php echo strtoupper($claim['resolution_type'] ?: 'Standard Repair'); ?>
                </div>
                <h4 style="margin: 0 0 15px 0; color: #1e293b; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Resolution Summary:</h4>
                <div style="padding: 20px; background: #f8fafc; border-radius: 8px; border-left: 4px solid var(--primary);">
                    <p style="margin: 0; color: #334155; line-height: 1.6;"><?php echo htmlspecialchars($claim['resolution_notes'] ?: 'Your claim has been successfully processed and resolved by our technical team.'); ?></p>
                </div>
                <div style="margin-top: 15px; font-size: 13px; color: #64748b;">
                    <i class="fas fa-calendar-alt"></i> Date Resolved: <?php echo date('M d, Y', strtotime($claim['updated_at'])); ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- IN PROGRESS / EVALUATION PLACEHOLDER -->
        <div style="margin-top:40px; padding: 40px; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; text-align: center;">
            <div style="font-size: 40px; color: #6366f1; margin-bottom: 15px;"><i class="fas fa-microchip fa-spin"></i></div>
            <h3 style="margin: 0; color: #1e293b;">Evaluation in Progress</h3>
            
            <?php if($claim['assigned_technician_id']): ?>
                <div style="margin-top: 15px; padding: 10px 20px; background: #eef2ff; border-radius: 30px; display: inline-block; color: #6366f1; font-weight: bold; font-size: 13px;">
                    <i class="fas fa-user-cog"></i> Assigned to Technical Expert: <?php echo htmlspecialchars($claim['technician_name'] ?? 'Authorized Technician'); ?>
                </div>
            <?php endif; ?>
            
            <p style="color: #64748b; font-size: 14px; margin-top: 15px; line-height: 1.6; max-width: 500px; margin-left: auto; margin-right: auto;">
                Our Technical Team is currently assessing your case. Once the evaluation is complete, the status will be updated here. 
                A formal **Resolution Statement** will be available once the final decision is authorized by management.
            </p>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; gap: 15px;">
            <a href="dashboard.php" class="btn" style="background: #64748b; color: white;"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <a href="view_warranty.php?id=<?php echo $claim['warranty_id']; ?>" class="btn" style="background: white; border: 1px solid #cbd5e1; color: #64748b;"><i class="fas fa-shield-alt"></i> View Related Warranty</a>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
