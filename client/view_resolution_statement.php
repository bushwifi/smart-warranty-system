<?php
// client/view_resolution_statement.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$claim_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

// Fetch full claim resolution details
$stmt = $pdo->prepare("
    SELECT c.*, p.product_name, p.model_number, p.brand, wr.serial_number, wr.warranty_number,
           cv.findings as tech_findings, cv.verified_at, t.full_name as tech_name,
           cr.resolution_type, cr.refund_amount, cr.resolution_notes, cr.resolved_at, o.full_name as owner_name
    FROM claims c
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    JOIN claim_verification cv ON c.id = cv.claim_id
    JOIN users t ON cv.technician_id = t.id
    LEFT JOIN claim_resolution cr ON c.id = cr.claim_id
    LEFT JOIN users o ON cr.repaired_by = o.id
    WHERE c.id = ? AND c.user_id = ?
");
$stmt->execute([$claim_id, $user_id]);
$data = $stmt->fetch();

if (!$data || $data['resolution_type'] != 'refund') {
    echo "This document is only available for authorized Refund resolutions.";
    exit();
}

$page_title = "Official Refund Statement - Claim #" . $data['claim_number'];
require_once '../includes/header.php';
?>

<div class="main-content" style="background: #f1f5f9; min-height: 100vh; padding: 40px 20px;">
    <!-- Print-friendly decision statement -->
    <div style="max-width: 800px; margin: 0 auto; background: #fff; padding: 50px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 40px;">
            <div>
                <h1 style="margin: 0; color: var(--primary); font-size: 28px;"><?php echo SITE_NAME; ?></h1>
                <p style="color: #64748b; margin-top: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Official Refund Authorization Report</p>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: bold; color: #1e293b;">Report ID: <?php echo htmlspecialchars($data['claim_number']); ?></div>
                <div style="color: #64748b; font-size: 14px;">Authorized: <?php echo date('F j, Y', strtotime($data['resolved_at'] ?? $data['updated_at'])); ?></div>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-size: 11px; margin-bottom: 10px;">Part A: Product & Transaction Details</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; background: #f8fafc; padding: 20px; border-radius: 8px;">
                <div>
                    <small style="color: #64748b;">Product</small>
                    <div style="font-weight: bold; font-size: 14px;"><?php echo htmlspecialchars($data['brand'] . ' ' . $data['product_name']); ?></div>
                </div>
                <div>
                    <small style="color: #64748b;">Serial Number</small>
                    <div style="font-weight: bold; font-size: 14px;"><code><?php echo htmlspecialchars($data['serial_number']); ?></code></div>
                </div>
                <div>
                    <small style="color: #64748b;">Refund Total</small>
                    <div style="font-weight: bold; font-size: 16px; color: var(--success);"><?php echo CURRENCY; ?><?php echo number_format($data['refund_amount'], 2); ?></div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 40px;">
            <h4 style="text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-size: 11px; margin-bottom: 10px;">Part B: Reasons for Decision</h4>
            
            <div style="margin-top: 15px; border-left: 4px solid #cbd5e1; padding: 5px 0 5px 25px;">
                <h5 style="margin: 0; color: #1e293b; font-size: 14px;">1. Technical Assessment Findings</h5>
                <p style="color: #475569; line-height: 1.6; margin-top: 10px; font-size: 15px;"><?php echo nl2br(htmlspecialchars($data['tech_findings'])); ?></p>
                <div style="margin-top: 10px; font-size: 12px; color: #94a3b8;">
                    Verified by Technician: <?php echo htmlspecialchars($data['tech_name']); ?>
                </div>
            </div>

            <div style="margin-top: 30px; border-left: 4px solid var(--success); padding: 5px 0 5px 25px;">
                <h5 style="margin: 0; color: #1e293b; font-size: 14px;">2. Management Authorization Reason</h5>
                <p style="color: #334155; line-height: 1.6; margin-top: 10px; font-size: 15px; font-style: italic;">
                    "<?php echo nl2br(htmlspecialchars($data['resolution_notes'] ?? 'This refund has been authorized following a complete technical review and verification of product eligibility.')); ?>"
                </p>
            </div>
        </div>

        <div style="margin-top: 80px; display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="text-align: center;">
                <div style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 20px; color: #1e293b; border-bottom: 2px solid #1e293b; padding: 0 50px;"><?php echo htmlspecialchars($data['owner_name'] ?? 'Executive Authorization'); ?></div>
                <small style="color: #64748b; display: block; margin-top: 10px; font-weight: bold; text-transform: uppercase;">Final Approving Officer</small>
            </div>
            <div style="text-align: right;">
                <button onclick="window.print()" class="btn" style="background: var(--primary); padding: 12px 30px;">
                    <i class="fas fa-print"></i> Download/Print PDF
                </button>
            </div>
        </div>

        <div style="margin-top: 60px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 11px;">
            This document serves as formal authorization for a refund and is computer-generated.<br>
            For any queries, please contact support with Reference #<?php echo $data['claim_number']; ?>.<br>
            Authorized and issued by <?php echo SITE_NAME; ?> Headquarters.
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="dashboard.php" style="color: #64748b; text-decoration: none; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .main-content, .main-content * { visibility: visible; }
    .main-content { position: absolute; left: 0; top: 0; width: 100%; padding: 0; }
    button { display: none !important; }
    .sidebar, .header-container { display: none !important; }
    a { display: none !important; }
}
</style>

<?php require_once '../includes/footer.php'; ?>
