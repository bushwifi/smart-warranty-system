<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Smart Warranty System';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . $site_name : $site_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>

<body>
    <div class="header">
        <div class="brand-section">
            <button id="sidebar-toggle" class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
            <h1>
                <?php echo $site_name; ?>
            </h1>
        </div>
        <div class="user-info">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="user-name"><i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
                <a href="<?php echo SITE_URL; ?>logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i>
                    Logout</a>
            <?php endif; ?>
        </div>
    </div>