<?php
// admin/dashboard.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Get system statistics
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM products) as total_products,
        (SELECT COUNT(*) FROM claims) as total_claims,
        (SELECT COUNT(*) FROM warranty_registrations) as total_warranties
")->fetch();

// Get recent users
$recent_users = $pdo->query("
    SELECT id, username, full_name, email, user_type, status, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll();

// Get recent system activities
$recent_activities = $pdo->query("
    SELECT a.*, u.username 
    FROM activity_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 10
")->fetchAll();

$page_title = "Admin Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-shield-alt"></i> System Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users icon"></i>
                <h3>Total Users</h3>
                <div class="value"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-box icon"></i>
                <h3>Total Products</h3>
                <div class="value"><?php echo number_format($stats['total_products'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-invoice icon"></i>
                <h3>Total Claims</h3>
                <div class="value"><?php echo number_format($stats['total_claims'] ?? 0); ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-certificate icon"></i>
                <h3>Registered Warranties</h3>
                <div class="value"><?php echo number_format($stats['total_warranties'] ?? 0); ?></div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-user-plus"></i> Recently Registered Users</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($recent_users) > 0): ?>
                        <?php foreach($recent_users as $user): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="status status-<?php echo ($user['user_type'] == 'admin' ? 'approved' : 'active'); ?>"><?php echo ucfirst(htmlspecialchars($user['user_type'])); ?></span></td>
                                <td>
                                    <span class="status status-<?php echo htmlspecialchars($user['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="empty-state">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="section">
        <h2><i class="fas fa-list-alt"></i> Recent System Logs</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($recent_activities) > 0): ?>
                        <?php foreach($recent_activities as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['username'] ?? 'System/Guest'); ?></td>
                                <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                                <td><?php echo date('M d, H:i:s', strtotime($activity['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty-state">No activities logged yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
