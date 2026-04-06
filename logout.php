<?php
session_start();

if (isset($_SESSION['user_id'])) {
    require_once 'config.php';
    logActivity($_SESSION['user_id'], 'User logged out');
}

session_destroy();
header("Location: login.php");
exit();
?>