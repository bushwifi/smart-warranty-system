<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'warranty_system');
$protocol = (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) || 
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    ($_SERVER['SERVER_PORT'] == 443)
) ? "https" : "http";

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Robust base_path calculation
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$current_dir = str_replace('\\', '/', __DIR__);
$base_path = str_replace($doc_root, '', $current_dir);
$base_path = '/' . trim($base_path, '/') . '/';
if ($base_path === '//') $base_path = '/';

define('SITE_URL', $protocol . '://' . $host . $base_path);
define('SITE_NAME', 'Smart Warranty System');
define('CURRENCY', 'KSh');

// Set timezone
date_default_timezone_set('UTC');

// Load Core Files
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize the global connection using PDO for legacy access compatibility temporarily
$pdo = Database::getInstance()->getConnection();