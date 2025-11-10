<?php
// AdminDashboard/transactions/actions.php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/repository/transactions_repo.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/transactions.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $id     = isset($_POST['transaction_id']) ? (int)$_POST['transaction_id'] : 0;

    if ($id <= 0 || !in_array($action, ['confirm','cancel','update'], true)) {
        header('Location: ' . BASE_URL . '/transactions.php?success=false&error=bad_request');
        exit;
    }

    $status = $action === 'confirm' ? 'confirmed'
           : ($action === 'cancel' ? 'cancelled' : 'pending');

    $ok = trx_set_status($conn, $id, $status);

    if ($ok) {
        header('Location: ' . BASE_URL . '/transactions.php?success=true&action=' . urlencode($action));
    } else {
        header('Location: ' . BASE_URL . '/transactions.php?success=false&error=save_failed');
    }
    exit;

} catch (Throwable $e) {
    error_log('transactions/actions error: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/transactions.php?success=false&error=exception');
    exit;
}
