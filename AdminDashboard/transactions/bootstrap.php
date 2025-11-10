<?php
// AdminDashboard/transactions/bootstrap.php
ob_start();
session_start();

// ---- DB ----
require_once __DIR__ . '/../../backend/db.php';
if (!isset($conn) || !($conn instanceof PDO)) {
    die("Database connection not established.");
}

// ---- Base URL helpers (for assets and links) ----
if (!defined('TRANS_DIR')) define('TRANS_DIR', __DIR__);
if (!defined('BASE_URL')) {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'];
  $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // e.g. /CCRO-Request/AdminDashboard/transactions
  define('BASE_URL', "$scheme://$host$base");
}

// ---- (Optional) Auth guard ----
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: ../admin.php");
//     exit;
// }
