<?php
// admin/claims.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = '';
$error = '';

// Handle manual status override
if (isset($_POST['override_status']) && isset($_POST['claim_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $claim_id = (int)$_POST['claim_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $pdo->prepare("UPDATE claims SET status = ? WHERE id = ?");
        if($stmt->execute([$new_status, $claim_id])) {
            $message = "Claim #$claim_id successfully updated to $new_status.";
        } else {
            $error = "Failed to override claim status.";
        }
    }
}

// Pagination Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$total_claims = $pdo->query("SELECT COUNT(*) FROM claims")->fetchColumn();
$total_pages = ceil($total_claims / $limit);

// Get all claims globally
$claims = $pdo->query("
    SELECT c.*, u.full_name as customer_name, p.product_name, wr.serial_number
    FROM claims c
    JOIN users u ON c.user_id = u.id
    JOIN warranty_registrations wr ON c.warranty_id = wr.id
    JOIN products p ON wr.product_id = p.id
    ORDER BY c.created_at DESC
    LIMIT $limit OFFSET $offset
")->fetchAll();

$page_title = "Global Claims Management";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-exclamation-triangle"></i> All System Claims</h2>
        
        <?php if($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Claim #</th>
                        <th>Customer</th>
                        <th>Product (SN)</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date Filed</th>
                        <th>Override Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($claims as $claim): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($claim['claim_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($claim['customer_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($claim['product_name']); ?>
                                <br><small>SN: <?php echo htmlspecialchars($claim['serial_number']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars(ucfirst($claim['issue_category'])); ?></td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($claim['status']); ?>">
                                    <?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($claim['status']))); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($claim['created_at'])); ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:5px; align-items:center;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="claim_id" value="<?php echo $claim['id']; ?>">
                                    <select name="new_status" style="width: auto; padding: 5px;">
                                        <option value="pending" <?php echo $claim['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="under_review" <?php echo $claim['status'] == 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                        <option value="approved" <?php echo $claim['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $claim['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        <option value="in_progress" <?php echo $claim['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="completed" <?php echo $claim['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="override_status" class="btn btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($total_pages > 1): ?>
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; align-items: center;">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-sm">Previous</a>
                <?php endif; ?>
                <span style="font-size: 14px; color: #666;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
