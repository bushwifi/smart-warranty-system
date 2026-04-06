<?php
require_once 'config.php';

if (isLoggedIn()) {
    $user_type = $_SESSION['user_type'] ?? '';
    switch($user_type) {
        case 'admin':
            header("Location: " . SITE_URL . "admin/dashboard.php");
            break;
        case 'technician':
            header("Location: " . SITE_URL . "technician/dashboard.php");
            break;
        case 'owner':
            header("Location: " . SITE_URL . "owner/dashboard.php");
            break;
        default:
            header("Location: " . SITE_URL . "client/dashboard.php");
    }
    exit();
}

// If not logged in, redirect to login page
header("Location: " . SITE_URL . "login.php");
exit();