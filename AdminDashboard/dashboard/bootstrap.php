<?php
// AdminDashboard/transactions/bootstrap.php
ob_start();
session_start();

// --- DB ---
require_once __DIR__ . '/../../backend/db.php';
if (!isset($conn) || !($conn instanceof PDO)) {
    die("Database connection not established.");
}

// --- Auth guard (uncomment if you want hard redirect for non-admins) ---
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: ../../admin.php");
//     exit;
// }

// Helpers
function base_url_from_script(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/CCRO-Request/AdminDashboard/transactions/hom'); // e.g. /CCRO-Request/AdminDashboard/transactions
    return "$scheme://$host$base";
}

// Runtime absolute base URL for this folder (e.g., /CCRO-Request/AdminDashboard/transactions)
if (!defined('BASE_URL')) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'];
  $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/CCRO-Request/AdminDashboard/transactions');
  define('BASE_URL', "$scheme://$host$base");
}

// Filesystem root of this folder (for filemtime cache-busting)
if (!defined('TRANS_DIR')) {
  define('TRANS_DIR', __DIR__);
}
