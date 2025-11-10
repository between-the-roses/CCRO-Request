<?php
session_start();
include '../backend/db.php';

if ($_SESSION['role'] !== 'Admin') {
  header('HTTP/1.1 403 Forbidden');
  exit("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $adminId = $_POST['admin_id'];
  $newRole = $_POST['new_role'];

  $stmt = $conn->prepare("UPDATE admin SET role = ? WHERE admin_id = ?");
  $stmt->execute([$newRole, $adminId]);

  header('Location: ../admins.php');
  exit;
}
?>
