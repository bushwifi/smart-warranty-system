<?php
// owner/resolved_claims.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

// Fetch all resolved claims
$resolved_claims = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, wr.serial_number,
           cr.resolution_type, cr.resolution_notes, cr.resolved_at, cr.refund_amount
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    JOIN claim_resolution cr ON c.id = cr.claim_id
    ORDER BY cr.resolved_at DESC
")->fetchAll();

$page_title = "Resolution History";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2><i class="fas fa-history"></i> Resolution History & Decisions</h2>
            <div style="background: #eef2ff; padding: 10px 20px; border-radius: 30px; color: var(--primary); font-weight: bold; font-size: 14px;">
                Total Resolutions: <?php echo count($resolved_claims); ?>
            </div>
        </div>

        <p style="color: var(--text-muted); margin-bottom: 30px;">A master record of all authorized decisions, refunds, and repair completions.</p>

        <div class="section" style="padding: 0; border: none; background: transparent;">
            <table>
                <thead>
                    <tr>
                        <th>Date Resolved</th>
                        <th>Claim #</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Decision Type</th>
                        <th>Amount/Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($resolved_claims) > 0): ?>
                        <?php foreach($resolved_claims as $claim): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($claim['resolved_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($claim['product_name']); ?></td>
                                <td>
                                    <span class="status status-<?php echo ($claim['resolution_type'] == 'refund') ? 'refunded' : (($claim['resolution_type'] == 'replacement') ? 'replaced' : 'completed'); ?>">
                                        <?php echo strtoupper($claim['resolution_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($claim['resolution_type'] == 'refund'): ?>
                                        <span style="color: #dc3545; font-weight: bold;"><?php echo CURRENCY; ?><?php echo number_format($claim['refund_amount'], 2); ?></span>
                                    <?php else: ?>
                                        <small style="color: #666;"><?php echo htmlspecialchars(substr($claim['resolution_notes'], 0, 30)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button onclick="showDecision('<?php echo addslashes($claim['claim_number']); ?>', '<?php echo addslashes($claim['resolution_notes']); ?>', '<?php echo $claim['resolution_type']; ?>')" class="btn btn-sm" style="background: var(--primary); color: white;">
                                        <i class="fas fa-search-plus"></i> View Decision
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                No resolutions have been recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Simple Modal for Decision Preview -->
<div id="decisionModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; width:90%; max-width:500px; padding:30px; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <h3 id="modalTitle" style="margin-top:0; color:var(--primary);"></h3>
        <hr style="margin: 15px 0; border: none; border-top: 1px solid #eee;">
        <div style="background:#f8fafc; padding:20px; border-radius:8px; border-left:4px solid var(--primary); margin: 20px 0;">
            <small style="color:#64748b; font-weight:bold; text-transform:uppercase; font-size:11px;">Authorized Decision Statement:</small>
            <p id="modalBody" style="margin-top:10px; line-height:1.6; color:#334155;"></p>
        </div>
        <button onclick="document.getElementById('decisionModal').style.display='none'" class="btn" style="width:100%;">Close Preview</button>
    </div>
</div>

<script>
function showDecision(claimNum, notes, type) {
    document.getElementById('modalTitle').innerText = "Claim #" + claimNum + " Final Decision (" + type.toUpperCase() + ")";
    document.getElementById('modalBody').innerText = notes;
    document.getElementById('decisionModal').style.display = 'flex';
}
</script>

<?php require_once '../includes/footer.php'; ?>
