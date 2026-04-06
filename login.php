<?php
require_once 'config.php';

$error = '';
$success = '';

// Handle Brute Force Mitigation
if (isset($_SESSION['lockout_time'])) {
    if (time() < $_SESSION['lockout_time']) {
        $remaining = ceil(($_SESSION['lockout_time'] - time()) / 60);
        $error = "Too many failed attempts. Please try again in {$remaining} minutes.";
    } else {
        unset($_SESSION['lockout_time']);
        $_SESSION['failed_login_attempts'] = 0;
    }
}

// Handle Login
if (isset($_POST['login']) && empty($error)) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please refresh and try again.";
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = ? AND status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['failed_login_attempts'] = 0;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Update last login
            $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update->execute([$user['id']]);
            
            // Log activity
            logActivity($user['id'], 'User logged in');
            
            // Redirect based on user type
            switch($user['user_type']) {
                case 'admin':
                    header("Location: admin/dashboard.php");
                    break;
                case 'technician':
                    header("Location: technician/dashboard.php");
                    break;
                case 'owner':
                    header("Location: owner/dashboard.php");
                    break;
                default:
                    header("Location: client/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
            $_SESSION['failed_login_attempts'] = ($_SESSION['failed_login_attempts'] ?? 0) + 1;
            if ($_SESSION['failed_login_attempts'] >= 5) {
                $_SESSION['lockout_time'] = time() + (5 * 60);
                $error = "Too many failed attempts. Please try again in 5 minutes.";
            }
        }
    } else {
        $error = "Username not found or account inactive!";
        $_SESSION['failed_login_attempts'] = ($_SESSION['failed_login_attempts'] ?? 0) + 1;
        if ($_SESSION['failed_login_attempts'] >= 5) {
            $_SESSION['lockout_time'] = time() + (5 * 60);
            $error = "Too many failed attempts. Please try again in 5 minutes.";
        }
    }
    }
}

// Handle Registration
if (isset($_POST['register']) && empty($error)) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token. Please refresh and try again.";
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $full_name = $_POST['full_name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
    
    // Check if username or email exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    
    if ($check->fetch()) {
        $error = "Username or email already exists!";
    } else {
        $insert = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone, address, user_type) VALUES (?, ?, ?, ?, ?, ?, 'client')");
        try {
            if ($insert->execute([$username, $password, $email, $full_name, $phone, $address])) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Registration failed.";
            }
        } catch (PDOException $e) {
             $error = "Registration failed: " . $e->getMessage();
        }
    }
    }
}

$page_title = "Login / Register";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Login/Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); width: 100%; max-width: 500px; padding: 40px; animation: slideUp 0.5s ease; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; font-size: 28px; margin-bottom: 10px; }
        .header p { color: #666; font-size: 14px; }
        .tab-buttons { display: flex; margin-bottom: 30px; background: #f5f5f5; padding: 5px; border-radius: 10px; }
        .tab-button { flex: 1; padding: 12px; background: none; border: none; cursor: pointer; font-size: 16px; color: #666; transition: all 0.3s; border-radius: 8px; font-weight: 500; }
        .tab-button.active { background: #667eea; color: white; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; font-size: 14px; }
        .input-group { position: relative; display: flex; align-items: center; }
        .input-group i { position: absolute; left: 15px; color: #999; font-size: 16px; }
        input, textarea { width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; transition: all 0.3s; font-family: inherit; }
        textarea { padding-left: 15px; resize: vertical; }
        input:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .password-toggle { position: absolute; right: 15px; cursor: pointer; color: #999; }
        .btn { width: 100%; padding: 14px; background: #667eea; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 10px; }
        .btn:hover { background: #5a67d8; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3); }
        .message { padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
        .error { background: #fee; color: #c33; border: 1px solid #fcc; }
        .success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .tab-content.active { display: block; }
        .footer { text-align: center; margin-top: 20px; color: #999; font-size: 12px; }
        .footer a { color: #667eea; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo SITE_NAME; ?></h1>
            <p>Manage your warranties with ease</p>
        </div>
        
        <?php if($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="tab-buttons">
            <button class="tab-button active" onclick="showTab('login')">Login</button>
            <button class="tab-button" onclick="showTab('register')">Register</button>
        </div>
        
        <!-- Login Form -->
        <div id="login" class="tab-content active">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Enter your username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="login-password" placeholder="Enter your password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('login-password', this)"></i>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
        
        <!-- Registration Form -->
        <div id="register" class="tab-content">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Choose a username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="register-password" placeholder="Create a password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('register-password', this)"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-group">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="full_name" placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <div class="input-group">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" placeholder="Enter your phone number" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3" placeholder="Enter your address" required></textarea>
                </div>
                
                <button type="submit" name="register" class="btn">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
        </div>
        
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>