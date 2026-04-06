<?php
// owner/users.php
require_once '../config.php';
requireLogin();

if ($_SESSION['user_type'] != 'owner') {
    header("Location: ../index.php");
    exit();
}

$owner_id = (int)$_SESSION['user_id'];
$message = ''; $error = '';

// Handle creating a new technician
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tech'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone, user_type, status) VALUES (?, ?, ?, ?, ?, 'technician', 'active')");
            $stmt->execute([$username, $password, $email, $full_name, $phone]);
            $message = "Technician account '{$username}' created successfully!";
            logActivity($owner_id, "Created technician account: $full_name");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "The username or email is already in use.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Handle Ban/Activate actions
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Security token validation failed.";
    } else {
        $target_id = (int)$_POST['user_id'];
        $action = $_POST['action'];
        
        if ($target_id !== $owner_id) {
            if ($action === 'ban') {
                $pdo->prepare("UPDATE users SET status = 'inactive' WHERE id = ?")->execute([$target_id]);
                $message = "Staff account has been disabled.";
            } elseif ($action === 'activate') {
                $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$target_id]);
                $message = "Staff account has been activated.";
            }
            logActivity($owner_id, "Updated status for user #$target_id to $action");
        }
    }
}

// Get all staff (Technicians)
$staff = $pdo->query("SELECT id, username, full_name, email, phone, status, created_at FROM users WHERE user_type = 'technician' ORDER BY created_at DESC")->fetchAll();

$page_title = "Manage Technical Staff";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="section">
        <h2><i class="fas fa-user-plus"></i> Add New Technician</h2>
        <?php if($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" action="" class="form-container" style="max-width: 100%; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required placeholder="John Technician">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="tech_john">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="john@warranty.com">
            </div>
            <div class="form-group">
                <label>Temporary Password</label>
                <input type="password" name="password" required placeholder="Minimum 6 characters" minlength="6">
            </div>
            <div class="form-group">
                <label>Phone (Optional)</label>
                <input type="text" name="phone" placeholder="+00 000 000 000">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="submit" name="create_tech" class="btn" style="width: 100%; height: 45px;">Register Technician Account</button>
            </div>
        </form>
    </div>

    <div class="section">
        <h2><i class="fas fa-users-cog"></i> Technical Staff Management</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Technician</th>
                        <th>Username</th>
                        <th>Contact</th>
                        <th>Current Status</th>
                        <th>Joining Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($staff) > 0): ?>
                        <?php foreach($staff as $u): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
                                <td><code>@<?php echo htmlspecialchars($u['username']); ?></code></td>
                                <td><?php echo htmlspecialchars($u['email']); ?><br><small><?php echo htmlspecialchars($u['phone']); ?></small></td>
                                <td>
                                    <span class="badge <?php echo $u['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo ucfirst($u['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm" style="background: var(--primary); padding: 5px 10px;" title="Edit Profile">
                                            <i class="fas fa-user-edit"></i>
                                        </a>
                                        
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <?php if($u['status'] === 'active'): ?>
                                                <button type="submit" name="action" value="ban" class="btn btn-sm" style="background: #f59e0b; padding: 5px 10px;" title="Disable Account">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="activate" class="btn btn-sm" style="background: #28a745; padding: 5px 10px;" title="Activate Account">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>

                                        <form method="POST" action="delete_user.php" onsubmit="return confirm('Permanently delete this technician account? This cannot be undone.');" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                            <button type="submit" class="btn btn-sm" style="background: #ef4444; padding: 5px 10px;" title="Delete Account">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="empty-state">No technician accounts found. Register one above!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
