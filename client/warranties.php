<?php
// client/warranties.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'client') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$warranties = $pdo->query("
    SELECT wr.*, p.product_name, p.model_number, p.brand 
    FROM warranty_registrations wr
    JOIN products p ON wr.product_id = p.id
    WHERE wr.user_id = $user_id 
    ORDER BY wr.created_at DESC
")->fetchAll();

$page_title = "My Warranties";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;"><i class="fas fa-file-contract"></i> My Registered Warranties</h2>
            <a href="check_warranty.php" class="btn"><i class="fas fa-plus"></i> Register New via Serial</a>
        </div>
        
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Warranty #</th>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Serial Number</th>
                        <th>Ends On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($warranties) > 0): ?>
                        <?php foreach($warranties as $warranty): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($warranty['warranty_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($warranty['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($warranty['model_number']); ?></td>
                                <td><?php echo htmlspecialchars($warranty['serial_number']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($warranty['warranty_end_date'])); ?></td>
                                <td>
                                    <?php 
                                        $is_active = strtotime($warranty['warranty_end_date']) >= strtotime(date('Y-m-d'));
                                        $statusClass = $is_active ? 'active' : 'expired';
                                        $statusText = $is_active ? 'Active' : 'Expired';
                                    ?>
                                    <span class="status status-<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><a href="view_warranty.php?id=<?php echo htmlspecialchars($warranty['id']); ?>" class="btn btn-sm">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="empty-state">No warranties registered yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
