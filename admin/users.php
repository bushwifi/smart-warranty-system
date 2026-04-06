<?php
// admin/users.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$message = '';
$error = '';

// Handle actions (Ban/Activate)
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $target_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        // Prevent modifying self
        if ($target_id !== (int)$_SESSION['user_id']) {
            if ($action === 'ban') {
                $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?")->execute([$target_id]);
                $message = "User has been banned.";
            } elseif ($action === 'activate') {
                $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$target_id]);
                $message = "User has been activated.";
            }
        } else {
            $error = "You cannot modify your own account status.";
        }
    }
}

// Pagination Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Get all users
$users = $pdo->query("
    SELECT id, username, full_name, email, phone, user_type, status, created_at 
    FROM users 
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
")->fetchAll();

$page_title = "Manage Users";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-users"></i> System Users</h2>
        
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
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td><span class="status status-<?php echo ($user['user_type'] == 'admin' ? 'approved' : 'active'); ?>"><?php echo ucfirst(htmlspecialchars($user['user_type'])); ?></span></td>
                            <td>
                                <span class="status status-<?php echo htmlspecialchars($user['status']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <?php if($user['status'] === 'active'): ?>
                                            <button type="submit" name="action" value="ban" class="btn btn-sm" style="background: #dc3545;">Ban</button>
                                        <?php else: ?>
                                            <button type="submit" name="action" value="activate" class="btn btn-sm" style="background: #28a745;">Activate</button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">(You)</span>
                                <?php endif; ?>
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
