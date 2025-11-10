<?php
// AdminDashboard/transactions/transactions.php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/repository/transactions_repo.php';

// Pagination
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

try {
    $total_records = trx_total_records($conn);
    $total_pages   = max(1, (int)ceil($total_records / $limit));
    $start_record  = $total_records ? ($offset + 1) : 0;
    $end_record    = min($offset + $limit, $total_records);

    $transactions = trx_fetch_page($conn, $limit, $offset);

    // Optional detail (if you want to prefetch when ?id= is present)
    $transaction_detail = null;
    if (isset($_GET['id'])) {
        $transaction_detail = trx_fetch_detail($conn, (int)$_GET['id']);
    }

} catch (Throwable $e) {
    error_log('transactions/transactions error: ' . $e->getMessage());
    $transactions = [];
    $total_records = 0; $total_pages = 1; $start_record = 0; $end_record = 0;
    $_GET['success'] = 'false'; $_GET['error'] = 'db_error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Transactions | CCRO System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/transactions.css?v=<?= filemtime(TRANS_DIR.'/assets/transactions.css') ?>">
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<div class="main-content" id="mainContent">
  <div class="page-title">View Transactions</div>
  <div class="page-subtitle">Manage and view all document transactions</div>

  <?php include __DIR__ . '/partials/alerts.php'; ?>

  <div class="table-container">
    <div class="table-header">
      <!-- <div class="export-buttons">
        <button class="btn-excel" id="exportExcel"><i class='bx bx-table'></i> Excel</button>
        <button class="btn-pdf"   id="exportPDF"><i class='bx bx-file-pdf'></i> PDF</button>
        <button class="btn-print" id="printData"><i class='bx bx-printer'></i> Print</button>
      </div> -->
      <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search transactions..." class="form-control">
      </div>
    </div>

    <?php
      $tableData = [
        'transactions'  => $transactions,
      ];
      include __DIR__ . '/partials/table.php';
    ?>

    <?php
      $pagination = [
        'page'          => $page,
        'total_pages'   => $total_pages,
        'total_records' => $total_records,
        'start_record'  => $start_record,
        'end_record'    => $end_record
      ];
      include __DIR__ . '/partials/pagination.php';
    ?>
  </div>
</div>

<?php include __DIR__ . '/partials/modal.php'; ?>

<script>
  window.TRANS_BASE = "<?= BASE_URL ?>";
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/transactions.js?v=<?= filemtime(TRANS_DIR.'/assets/transactions.js') ?>"></script>
</body>
</html>
