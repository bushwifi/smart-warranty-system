<?php
// technician/complete_repair.php
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

// Fetch Claim & Verification
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as customer_name,
           p.product_name, p.model_number,
           cv.findings, cv.recommended_action
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    LEFT JOIN claim_verification cv ON c.id = cv.claim_id
    WHERE c.id = ? AND c.status = 'in_progress'
");
$stmt->execute([$claim_id]);
$claim = $stmt->fetch();

if (!$claim) {
    // If not in progress, maybe it's already completed or not authorized
    header("Location: dashboard.php");
    exit();
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_repair'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $resolution_notes = trim($_POST['resolution_notes']);
        $resolution_type = 'repair'; // Technicians are now restricted to repairs
        
        $pdo->beginTransaction();
        try {
            // Update Resolution table
            $resStmt = $pdo->prepare("
                INSERT INTO claim_resolution (claim_id, repaired_by, resolution_type, resolution_notes)
                VALUES (?, ?, ?, ?)
            ");
            $resStmt->execute([$claim_id, $technician_id, $resolution_type, $resolution_notes]);
            
            // Update Claim status
            $new_claim_status = ($resolution_type == 'replacement') ? 'replaced' : 'completed';
            $statusStmt = $pdo->prepare("UPDATE claims SET status = ?, updated_at = NOW() WHERE id = ?");
            $statusStmt->execute([$new_claim_status, $claim_id]);

            // Log activity
            logActivity($technician_id, "Completed repair for claim #{$claim['claim_number']}");
            
            // Notify customer
            createNotification($claim['user_id'], 'Repair Completed', "Your device for claim #{$claim['claim_number']} has been repaired and is ready.", 'success');
            
            $pdo->commit();
            $message = "Repair marked as completed!";
            header("refresh:2;url=dashboard.php");
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error completing repair: " . $e->getMessage();
        }
    }
}

$page_title = "Complete Repair";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-check-double"></i> Complete Repair: <?php echo htmlspecialchars($claim['claim_number']); ?></h2>
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex:1; min-width: 300px; background: #f8f9fa; padding: 20px; border-radius: 8px;">
                <h3>Repair Details</h3>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($claim['customer_name']); ?></p>
                <p><strong>Product:</strong> <?php echo htmlspecialchars($claim['product_name']); ?></p>
                <p><strong>Initial Findings:</strong><br><?php echo nl2br(htmlspecialchars($claim['findings'])); ?></p>
                <p><strong>Recommended:</strong> <?php echo htmlspecialchars(ucfirst($claim['recommended_action'])); ?></p>
            </div>
            
            <div style="flex:2; min-width: 300px;">
                <form method="POST" action="" class="form-container" style="max-width: 100%; margin: 0;">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <h3>Resolution Summary</h3>
                    
                    <div class="form-group">
                        <label>Authorized Action</label>
                        <div style="background: #eef2ff; padding: 10px; border-radius: 5px; font-weight: bold; color: var(--primary);">
                            <i class="fas fa-hammer"></i> Authorized Task: REPAIR
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Resolution Notes (Visible to Client)</label>
                        <textarea name="resolution_notes" rows="5" required placeholder="Describe the work done, parts replaced, or result of the repair."></textarea>
                    </div>
                    
                    <button type="submit" name="complete_repair" class="btn" style="background:#28a745;">Mark as Completed</button>
                    <a href="dashboard.php" class="btn" style="background:#6c757d; text-align:center;">Back to Dashboard</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
