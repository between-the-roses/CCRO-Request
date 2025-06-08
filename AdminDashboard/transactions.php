<?php

/*
// Initialize variables
$transactions = [];
$conn = null;

// Include database connection with error handling
if (file_exists('../backend/db.php')) {
    include 'config/db_connect.php';

    if (isset($conn) && $conn) {
        $sql = "SELECT * FROM transactions ORDER BY id DESC LIMIT 15";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        }

        if (isset($_POST['action']) && isset($_POST['transaction_id'])) {
            $action = $_POST['action'];
            $transaction_id = $_POST['transaction_id'];
            $admin_email = 'bernadette@ccro.gov.ph';
            $admin_name = 'Bernadette Marande';
            $date_now = date('Y-m-d H:i:s');

            if ($action === 'confirm') {
                $update_sql = "UPDATE transactions SET status = 'Confirmed' WHERE id = ?";
                $log_sql = "INSERT INTO transaction_history (transaction_id, staff, date_time, action) VALUES (?, ?, ?, 'Confirmed')";
            } elseif ($action === 'cancel') {
                $update_sql = "UPDATE transactions SET status = 'Cancelled' WHERE id = ?";
                $log_sql = "INSERT INTO transaction_history (transaction_id, staff, date_time, action) VALUES (?, ?, ?, 'Cancelled')";
            } elseif ($action === 'update') {
                $update_sql = "UPDATE transactions SET last_updated = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($stmt, 'si', $date_now, $transaction_id);
                $log_sql = "INSERT INTO transaction_history (transaction_id, staff, date_time, action) VALUES (?, ?, ?, 'Updated')";
            }

            if (isset($update_sql)) {
                if ($action !== 'update') {
                    $stmt = mysqli_prepare($conn, $update_sql);
                    mysqli_stmt_bind_param($stmt, 'i', $transaction_id);
                }

                $success = mysqli_stmt_execute($stmt);

                if ($success) {
                    $log_stmt = mysqli_prepare($conn, $log_sql);
                    mysqli_stmt_bind_param($log_stmt, 'iss', $transaction_id, $admin_email, $date_now);
                    mysqli_stmt_execute($log_stmt);

                    header("Location: transactions.php?success=true&action=$action");
                    exit;
                }
            }
        }

        if (isset($_GET['id'])) {
            $transaction_id = $_GET['id'];
            $detail_sql = "SELECT t.*, th.staff, th.date_time, th.action 
                          FROM transactions t 
                          LEFT JOIN transaction_history th ON t.id = th.transaction_id 
                          WHERE t.id = ?
                          ORDER BY th.date_time DESC";

            $stmt = mysqli_prepare($conn, $detail_sql);
            mysqli_stmt_bind_param($stmt, 'i', $transaction_id);
            mysqli_stmt_execute($stmt);
            $detail_result = mysqli_stmt_get_result($stmt);

            if ($detail_result) {
                $transaction_detail = mysqli_fetch_assoc($detail_result);
                $transaction_history = [];
                mysqli_data_seek($detail_result, 0);
                while ($history_row = mysqli_fetch_assoc($detail_result)) {
                    if (!empty($history_row['staff'])) {
                        $transaction_history[] = [
                            'staff' => $history_row['staff'],
                            'date_time' => $history_row['date_time'],
                            'action' => $history_row['action']
                        ];
                    }
                }
                mysqli_free_result($detail_result);
            }
        }

        mysqli_close($conn);
    } else {
        error_log("Database connection failed");
    }
} else {
    error_log("Database configuration file not found");
}
*/




// Sample data only — no database connection
$transactions = [
    [
        'id' => 1,
        'transaction_no' => 'TRX-2025001',
        'requesting_party' => 'Juan Dela Cruz',
        'contact_number' => '09171234567',
        'relationship' => 'Self',
        'document_type' => 'Birth Certificate',
        'appointment_date' => '2025-05-20',
        'appointment_time' => '10:00 AM',
        'payment_mode' => 'GCash',
        'status' => 'Pending'
    ],
    [
        'id' => 2,
        'transaction_no' => 'TRX-2025002',
        'requesting_party' => 'Maria Santos',
        'contact_number' => '09281234567',
        'relationship' => 'Mother',
        'document_type' => 'Marriage Certificate',
        'appointment_date' => '2025-05-22',
        'appointment_time' => '2:30 PM',
        'payment_mode' => 'Cash',
        'status' => 'Confirmed'
    ],
    [
        'id' => 3,
        'transaction_no' => 'TRX-2025003',
        'requesting_party' => 'Pedro Reyes',
        'contact_number' => '09081234567',
        'relationship' => 'Brother',
        'document_type' => 'Death Certificate',
        'appointment_date' => '2025-05-23',
        'appointment_time' => '11:15 AM',
        'payment_mode' => 'PayMaya',
        'status' => 'Cancelled'
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Transactions | Appointment System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body {
            background-color: #f5f6fa;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .brand h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .sidebar .nav-item {
            margin: 5px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0 25px 25px 0;
            margin-right: 20px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .user-info {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar .user-avatar {
            width: 40px;
            height: 40px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .sidebar .user-name {
            font-size: 14px;
            font-weight: 600;
        }
        
        .sidebar .user-email {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
            transition: all 0.3s ease;
        }
        
        .header.expanded {
            left: 60px;
        }
        
        .header .menu-toggle {
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }
        
        .header .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header .admin-avatar {
            width: 35px;
            height: 35px;
            background: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 30px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 60px);
        }
        
        .main-content.expanded {
            margin-left: 60px;
        }
        
        /* Table Container */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-top: 20px;
        }
        
        .table-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
        }
        
        .export-buttons button {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-excel {
            background: #28a745;
            color: white;
        }
        
        .btn-pdf {
            background: #dc3545;
            color: white;
        }
        
        .btn-print {
            background: #6c757d;
            color: white;
        }
        
        .export-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .search-container {
            flex: 1;
            max-width: 300px;
            margin-left: auto;
        }
        
        .search-container input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        /* Table Styles */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .custom-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            padding: 15px 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        
        .custom-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Status badges */
        .status-confirmed {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-cancelled {
            color: #dc3545;
            font-weight: 600;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: 600;
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 5px;
        }
        
        .pagination .page-item {
            list-style: none;
        }
        
        .pagination .page-link {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #6c757d;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .pagination .page-item.active .page-link {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #ccc;
            pointer-events: none;
        }
        
        .pagination .page-link:hover:not(.disabled) {
            background: #f8f9fa;
            border-color: #adb5bd;
        }
        
        /* Error message */
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        /* Success message */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        /* Page title */
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Transaction Detail Modal */
        .modal-xl {
            max-width: 1140px;
        }
        
        .transaction-details {
            padding: 20px;
        }
        
        .transaction-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .certificate-type-container {
            border-left: 3px solid #3498db;
            padding-left: 15px;
            margin-top: 15px;
        }
        
        .certificate-type-label {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .transaction-history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .transaction-history-table th,
        .transaction-history-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .transaction-history-table th {
            font-weight: 600;
            text-align: left;
            background-color: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-end;
        }
        
        .btn-confirm {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-update {
            background-color: #17a2b8;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .btn-print {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 600;
            cursor: pointer;
        }
        
        /* Certificate type checkbox styles */
        .certificate-checkbox-container {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .certificate-checkbox-option {
            margin-bottom: 10px;
        }
        
        .certificate-checkbox-option label {
            display: flex;
            align-items: center;
            font-size: 14px;
            cursor: pointer;
        }
        
        .certificate-checkbox-option input[type="checkbox"] {
            margin-right: 10px;
        }
        
        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            
            .print-container, .print-container * {
                visibility: visible;
            }
            
            .print-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        /* Clickable ID */
        .transaction-id {
            color: #3498db;
            text-decoration: underline;
            cursor: pointer;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            
            .header {
                left: 60px;
            }
            
            .main-content {
                margin-left: 60px;
            }
            
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-container {
                max-width: none;
                margin-left: 0;
            }
        }
        
        /* Transaction Paper Form */
        .paper-form {
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .paper-form-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .paper-form-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .paper-form-subtitle {
            font-size: 16px;
        }
        
        .paper-form-section {
            margin-bottom: 20px;
        }
        
        .paper-form-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .paper-form-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .paper-form-label {
            width: 40%;
            font-weight: 600;
        }
        
        .paper-form-value {
            width: 60%;
        }
        
        .paper-form-footer {
            margin-top: 30px;
            text-align: center;
        }
        
        .paper-form-signature {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .paper-form-signature-box {
            width: 45%;
            text-align: center;
        }
        
        .paper-form-signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            margin-bottom: 5px;
        }
        
        /* Certificate Checklist Styles */
        .certificate-checklist {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .certificate-checklist-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .certificate-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #eee;
        }
        
        .certificate-item label {
            margin-bottom: 0;
            margin-left: 10px;
            cursor: pointer;
        }
        
        .certificate-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        /* Transaction History Styles */
        .transaction-history {
            margin-top: 20px;
        }
        
        .transaction-history-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .staff-name {
            font-weight: 600;
            color: #3498db;
        }
        
        .history-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #eee;
        }
        
        .history-item i {
            margin-right: 10px;
            color: #3498db;
        }
        
        .history-item-content {
            flex: 1;
        }
        
        .history-timestamp {
            font-size: 12px;
            color: #6c757d;
        }
        
        .update-form-row {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h4>Appointment System</h4>
        </div>
        
        <nav class="nav flex-column mt-3">
            <div class="nav-item">
                <a class="nav-link" href='./includes/home.php'>
                    <i class='bx bx-home'></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link active" href="transactions.php">
                    <i class='bx bx-list-ul'></i>
                    <span>View Transactions</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="report.php">
                    <i class='bx bx-bar-chart-alt-2'></i>
                    <span>Report</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class='bx bx-cog'></i>
                    <span>Settings</span>
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class='bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
        
        <div class="user-info">
            <div class="user-avatar">
                <span>B</span>
            </div>
            <div class="user-name">Bernadette Marande</div>
            <div class="user-email">bernadette@ccro.gov.ph</div>
        </div>
    </div>

    <!-- Header -->
    <div class="header" id="header">
        <button class="menu-toggle" id="menuToggle">
            <i class='bx bx-menu'></i>
        </button>
        
        <div class="admin-info">
            <span>Administrator</span>
            <div class="admin-avatar">
                <i class='bx bx-user'></i>
            </div>
            <i class='bx bx-chevron-down'></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="page-title">View Transactions</div>
        <div class="page-subtitle">Manage and view all document transactions</div>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
            <div class="success-message">
                <?php 
                    $action = $_GET['action'] ?? 'update';
                    $actionText = '';
                    switch ($action) {
                        case 'confirm':
                            $actionText = 'confirmed';
                            break;
                        case 'cancel':
                            $actionText = 'cancelled';
                            break;
                        default:
                            $actionText = 'updated';
                    }
                ?>
                <strong>Success!</strong> Transaction has been <?php echo $actionText; ?> successfully.
            </div>
        <?php endif; ?>
        
        <?php if (empty($transactions)): ?>
            <div class="error-message">
                <strong>Database Connection Error:</strong> Unable to connect to the database. Please check your database configuration.
            </div>
        <?php endif; ?>
        
        <div class="table-container">
            <div class="table-header">
                <div class="export-buttons">
                    <button class="btn-excel" id="exportExcel">
                        <i class='bx bx-table'></i> Excel
                    </button>
                    <button class="btn-pdf" id="exportPDF">
                        <i class='bx bx-file-pdf'></i> PDF
                    </button>
                    <button class="btn-print" id="printData">
                        <i class='bx bx-printer'></i> Print
                    </button>
                </div>
                
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search transactions..." class="form-control">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Transaction No.</th>
                            <th>Requesting Party</th>
                            <th>Contact Number</th>
                            <th>Relationship</th>
                            <th>Document Type</th>
                            <th>Mode of Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($transactions)): ?>
                            <tr>
                                <td colspan="8" class="text-center" style="padding: 40px;">
                                    <i class='bx bx-info-circle' style="font-size: 48px; color: #ddd;"></i>
                                    <div style="margin-top: 10px; color: #6c757d;">No transactions found</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($transactions as $transaction): ?>
                                <tr>
                                    <td>
                                        <span class="transaction-id" data-bs-toggle="modal" data-bs-target="#transactionModal" data-id="<?php echo htmlspecialchars($transaction['id'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($transaction['id'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($transaction['transaction_no'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['requesting_party'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['contact_number'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['relationship'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['document_type'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['payment_mode'] ?? ''); ?></td>
                                    <td>
                                        <?php 
                                        $status = $transaction['status'] ?? 'Pending';
                                        if($status == 'Confirmed'): 
                                        ?>
                                            <span class="status-confirmed">Confirmed</span>
                                        <?php elseif($status == 'Cancelled'): ?>
                                            <span class="status-cancelled">Cancelled</span>
                                        <?php else: ?>
                                            <span class="status-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-container">
                <div>Showing 1 to <?php echo count($transactions); ?> of <?php echo count($transactions); ?> entries</div>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="transaction-details">
                                <div class="transaction-detail-header">
                                    <h5>Transaction Details</h5>
                                    <span id="transactionStatus" class="status-pending">Pending</span>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="transactionId">Transaction ID</label>
                                            <input type="text" class="form-control" id="transactionId" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="transactionNo">Transaction No.</label>
                                            <input type="text" class="form-control" id="transactionNo" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="documentType">Document Type</label>
                                            <input type="text" class="form-control" id="documentType" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="paymentMode">Mode of Payment</label>
                                            <input type="text" class="form-control" id="paymentMode" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-title">Personal Information</div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="requestingParty">Requesting Party</label>
                                            <input type="text" class="form-control" id="requestingParty" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="contactNumber">Contact Number</label>
                                            <input type="text" class="form-control" id="contactNumber" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                             <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="relationship">Relationship</label>
                                            <input type="text" class="form-control" id="relationship" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" id="address" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="purpose">Purpose</label>
                                            <input type="text" class="form-control" id="purpose" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="dateCreated">Date Created</label>
                                            <input type="text" class="form-control" id="dateCreated" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Certificate Types Checklist -->
                                <div class="certificate-checklist">
                                    <div class="certificate-checklist-title">Certificate Types</div>
                                    <div id="certificateTypes">
                                        <div class="certificate-item">
                                            <input type="checkbox" id="cert-birth" name="certificate[]" value="Birth Certificate">
                                            <label for="cert-birth">Birth Certificate</label>
                                        </div>
                                        <div class="certificate-item">
                                            <input type="checkbox" id="cert-death" name="certificate[]" value="Death Certificate">
                                            <label for="cert-death">Death Certificate</label>
                                        </div>
                                        <div class="certificate-item">
                                            <input type="checkbox" id="cert-marriage" name="certificate[]" value="Marriage Certificate">
                                            <label for="cert-marriage">Marriage Certificate</label>
                                        </div>
                                        <div class="certificate-item">
                                            <input type="checkbox" id="cert-cenomar" name="certificate[]" value="CENOMAR">
                                            <label for="cert-cenomar">CENOMAR</label>
                                        </div>
                                        <div class="certificate-item">
                                            <input type="checkbox" id="cert-fsis" name="certificate[]" value="FSIS">
                                            <label for="cert-fsis">FSIS</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transaction History -->
                                <div class="transaction-history">
                                    <div class="transaction-history-title">Transaction History</div>
                                    <div id="transactionHistory">
                                        <!-- History items will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <!-- Update Transaction Form -->
                            <div id="updateForm" style="display: none;">
                                <div class="section-title">Update Transaction</div>
                                <form id="transactionUpdateForm">
                                    <input type="hidden" id="updateTransactionId" name="transaction_id">
                                    
                                    <div class="update-form-row">
                                        <label for="updateStatus">Status</label>
                                        <select class="form-control" id="updateStatus" name="status">
                                            <option value="Pending">Pending</option>
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="update-form-row">
                                        <label for="updatePurpose">Purpose</label>
                                        <input type="text" class="form-control" id="updatePurpose" name="purpose">
                                    </div>
                                    
                                    <div class="update-form-row">
                                        <label for="updatePaymentMode">Payment Mode</label>
                                        <select class="form-control" id="updatePaymentMode" name="payment_mode">
                                            <option value="Cash">Cash</option>
                                            <option value="GCash">GCash</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                    
                                    <div class="update-form-row">
                                        <label for="updateNotes">Additional Notes</label>
                                        <textarea class="form-control" id="updateNotes" name="notes" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button class="btn-update" id="showUpdateForm">
                                    <i class='bx bx-edit'></i> Update Transaction
                                </button>
                                <button class="btn-print" id="printTransaction" style="display: none;">
                                    <i class='bx bx-printer'></i> Print Transaction
                                </button>
                                <button class="btn-confirm" id="confirmTransaction">
                                    <i class='bx bx-check'></i> Confirm
                                </button>
                                <button class="btn-cancel" id="cancelTransaction">
                                    <i class='bx bx-x'></i> Cancel
                                </button>
                            </div>
                            
                            <!-- Save/Cancel Update Buttons -->
                            <div class="action-buttons" id="updateButtons" style="display: none;">
                                <button class="btn-confirm" id="saveUpdate">
                                    <i class='bx bx-save'></i> Save Changes
                                </button>
                                <button class="btn-cancel" id="cancelUpdate">
                                    <i class='bx bx-x'></i> Cancel Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <h5 class="modal-title" id="printModalLabel">Print Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="print-container">
                        <div class="paper-form">
                            <div class="paper-form-header">
                                <div class="paper-form-title">CIVIL REGISTRATION OFFICE</div>
                                <div class="paper-form-subtitle">DOCUMENT REQUEST FORM</div>
                            </div>
                            
                            <div class="paper-form-section">
                                <div class="paper-form-section-title">TRANSACTION INFORMATION</div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Transaction ID:</div>
                                    <div class="paper-form-value" id="printTransactionId"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Transaction No.:</div>
                                    <div class="paper-form-value" id="printTransactionNo"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Date Created:</div>
                                    <div class="paper-form-value" id="printDateCreated"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Status:</div>
                                    <div class="paper-form-value" id="printStatus"></div>
                                </div>
                            </div>
                            
                            <div class="paper-form-section">
                                <div class="paper-form-section-title">REQUESTOR INFORMATION</div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Requesting Party:</div>
                                    <div class="paper-form-value" id="printRequestingParty"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Contact Number:</div>
                                    <div class="paper-form-value" id="printContactNumber"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Relationship:</div>
                                    <div class="paper-form-value" id="printRelationship"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Address:</div>
                                    <div class="paper-form-value" id="printAddress"></div>
                                </div>
                            </div>
                            
                            <div class="paper-form-section">
                                <div class="paper-form-section-title">DOCUMENT INFORMATION</div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Document Type:</div>
                                    <div class="paper-form-value" id="printDocumentType"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Purpose:</div>
                                    <div class="paper-form-value" id="printPurpose"></div>
                                </div>
                                <div class="paper-form-row">
                                    <div class="paper-form-label">Payment Mode:</div>
                                    <div class="paper-form-value" id="printPaymentMode"></div>
                                </div>
                            </div>
                            
                            <div class="paper-form-section">
                                <div class="paper-form-section-title">CERTIFICATE TYPES REQUESTED</div>
                                <div id="printCertificateTypes"></div>
                            </div>
                            
                            <div class="paper-form-footer">
                                <div class="paper-form-signature">
                                    <div class="paper-form-signature-box">
                                        <div class="paper-form-signature-line"></div>
                                        <div>Requestor Signature</div>
                                    </div>
                                    <div class="paper-form-signature-box">
                                        <div class="paper-form-signature-line"></div>
                                        <div>Staff Signature</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer no-print">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Sample transaction data for demo
        const transactionData = {
            1: {
                id: '1',
                transaction_no: 'TXN-2024-001',
                requesting_party: 'Juan Dela Cruz',
                contact_number: '09123456789',
                relationship: 'Self',
                address: '123 Sample Street, Sample City',
                document_type: 'Birth Certificate',
                payment_mode: 'Cash',
                purpose: 'Employment',
                status: 'Pending',
                date_created: '2024-01-15 10:30:00',
                notes: '',
                certificate_types: ['Birth Certificate'],
                history: [
                    {
                        action: 'Transaction Created',
                        timestamp: '2024-01-15 10:30:00',
                        staff: null,
                        notes: 'Initial transaction created'
                    }
                ]
            },
            2: {
                id: '2',
                transaction_no: 'TXN-2024-002',
                requesting_party: 'Maria Santos',
                contact_number: '09987654321',
                relationship: 'Mother',
                address: '456 Another Street, Another City',
                document_type: 'Death Certificate',
                payment_mode: 'GCash',
                purpose: 'Legal Purposes',
                status: 'Confirmed',
                date_created: '2024-01-14 14:20:00',
                notes: 'Urgent processing requested',
                certificate_types: ['Death Certificate', 'FSIS'],
                history: [
                    {
                        action: 'Transaction Created',
                        timestamp: '2024-01-14 14:20:00',
                        staff: null,
                        notes: 'Initial transaction created'
                    },
                    {
                        action: 'Transaction Confirmed',
                        timestamp: '2024-01-14 16:45:00',
                        staff: 'Bernadette Marande',
                        notes: 'Documents verified and approved'
                    }
                ]
            }
        };

        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('expanded');
            mainContent.classList.toggle('expanded');
        });

        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.custom-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Transaction modal functionality
        const transactionModal = document.getElementById('transactionModal');
        const transactionIds = document.querySelectorAll('.transaction-id');

        transactionIds.forEach(id => {
            id.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-id');
                loadTransactionDetails(transactionId);
            });
        });

        function loadTransactionDetails(transactionId) {
            const transaction = transactionData[transactionId];
            if (!transaction) {
                console.error('Transaction not found');
                return;
            }

            // Populate basic fields
            document.getElementById('transactionId').value = transaction.id;
            document.getElementById('transactionNo').value = transaction.transaction_no;
            document.getElementById('requestingParty').value = transaction.requesting_party;
            document.getElementById('contactNumber').value = transaction.contact_number;
            document.getElementById('relationship').value = transaction.relationship;
            document.getElementById('address').value = transaction.address;
            document.getElementById('documentType').value = transaction.document_type;
            document.getElementById('paymentMode').value = transaction.payment_mode;
            document.getElementById('purpose').value = transaction.purpose;
            document.getElementById('dateCreated').value = transaction.date_created;

            // Update status display
            const statusElement = document.getElementById('transactionStatus');
            statusElement.textContent = transaction.status;
            statusElement.className = `status-${transaction.status.toLowerCase()}`;

            // Check certificate types based on admin role
            const certificateCheckboxes = document.querySelectorAll('#certificateTypes input[type="checkbox"]');
            certificateCheckboxes.forEach(checkbox => {
                checkbox.checked = transaction.certificate_types.includes(checkbox.value);
                // Only enable for admin users (you can add user role check here)
                checkbox.disabled = false; // Set to true for non-admin users
            });

            // Load transaction history
            loadTransactionHistory(transaction.history);

            // Show/hide buttons based on status
            updateActionButtons(transaction.status);

            // Populate update form
            document.getElementById('updateTransactionId').value = transaction.id;
            document.getElementById('updateStatus').value = transaction.status;
            document.getElementById('updatePurpose').value = transaction.purpose;
            document.getElementById('updatePaymentMode').value = transaction.payment_mode;
            document.getElementById('updateNotes').value = transaction.notes;
        }

        function loadTransactionHistory(history) {
            const historyContainer = document.getElementById('transactionHistory');
            historyContainer.innerHTML = '';

            history.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'history-item';
                
                const icon = getHistoryIcon(item.action);
                const staffName = item.staff ? `<span class="staff-name">${item.staff}</span>` : 'System';
                
                historyItem.innerHTML = `
                    <i class='bx ${icon}'></i>
                    <div class="history-item-content">
                        <div>
                            <strong>${item.action}</strong> by ${staffName}
                        </div>
                        <div class="history-timestamp">${formatDateTime(item.timestamp)}</div>
                        ${item.notes ? `<div style="font-size: 12px; color: #6c757d; margin-top: 3px;">${item.notes}</div>` : ''}
                    </div>
                `;
                
                historyContainer.appendChild(historyItem);
            });
        }

        function getHistoryIcon(action) {
            switch (action) {
                case 'Transaction Created':
                    return 'bx-plus-circle';
                case 'Transaction Confirmed':
                    return 'bx-check-circle';
                case 'Transaction Cancelled':
                    return 'bx-x-circle';
                case 'Transaction Updated':
                    return 'bx-edit-alt';
                default:
                    return 'bx-info-circle';
            }
        }

        function formatDateTime(datetime) {
            const date = new Date(datetime);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }

        function updateActionButtons(status) {
            const confirmBtn = document.getElementById('confirmTransaction');
            const cancelBtn = document.getElementById('cancelTransaction');
            const printBtn = document.getElementById('printTransaction');
            const updateBtn = document.getElementById('showUpdateForm');

            if (status === 'Confirmed') {
                confirmBtn.style.display = 'none';
                cancelBtn.style.display = 'inline-block';
                printBtn.style.display = 'inline-block';
                updateBtn.style.display = 'inline-block';
            } else if (status === 'Cancelled') {
                confirmBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'none';
                printBtn.style.display = 'none';
                updateBtn.style.display = 'none';
            } else {
                confirmBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';
                printBtn.style.display = 'none';
                updateBtn.style.display = 'none';
            }
        }

        // Update form toggle
        document.getElementById('showUpdateForm').addEventListener('click', function() {
            const updateForm = document.getElementById('updateForm');
            const actionButtons = document.querySelector('.action-buttons');
            const updateButtons = document.getElementById('updateButtons');

            updateForm.style.display = 'block';
            actionButtons.style.display = 'none';
            updateButtons.style.display = 'flex';
        });

        document.getElementById('cancelUpdate').addEventListener('click', function() {
            const updateForm = document.getElementById('updateForm');
            const actionButtons = document.querySelector('.action-buttons');
            const updateButtons = document.getElementById('updateButtons');

            updateForm.style.display = 'none';
            actionButtons.style.display = 'flex';
            updateButtons.style.display = 'none';
        });

        // Save update
        document.getElementById('saveUpdate').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('transactionUpdateForm'));
            
            // Get certificate types
            const certificates = [];
            document.querySelectorAll('#certificateTypes input[type="checkbox"]:checked').forEach(cb => {
                certificates.push(cb.value);
            });
            
            // Add to transaction history
            const transactionId = formData.get('transaction_id');
            const transaction = transactionData[transactionId];
            
            transaction.history.push({
                action: 'Transaction Updated',
                timestamp: new Date().toISOString(),
                staff: 'Bernadette Marande', // Current logged in staff
                notes: `Updated by admin. New status: ${formData.get('status')}`
            });
            
            // Update transaction data
            transaction.status = formData.get('status');
            transaction.purpose = formData.get('purpose');
            transaction.payment_mode = formData.get('payment_mode');
            transaction.notes = formData.get('notes');
            transaction.certificate_types = certificates;
            
            // Refresh the modal display
            loadTransactionDetails(transactionId);
            
            // Hide update form
            document.getElementById('cancelUpdate').click();
            
            alert('Transaction updated successfully!');
        });

        // Confirm transaction
        document.getElementById('confirmTransaction').addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            const transaction = transactionData[transactionId];
            
            transaction.status = 'Confirmed';
            transaction.history.push({
                action: 'Transaction Confirmed',
                timestamp: new Date().toISOString(),
                staff: 'Bernadette Marande', // Current logged in staff
                notes: 'Transaction confirmed by admin'
            });
            
            // Refresh the modal display
            loadTransactionDetails(transactionId);
            
            alert('Transaction confirmed successfully!');
        });

        // Cancel transaction
        document.getElementById('cancelTransaction').addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            const transaction = transactionData[transactionId];
            
            transaction.status = 'Cancelled';
            transaction.history.push({
                action: 'Transaction Cancelled',
                timestamp: new Date().toISOString(),
                staff: 'Bernadette Marande', // Current logged in staff
                notes: 'Transaction cancelled by admin'
            });
            
            // Refresh the modal display
            loadTransactionDetails(transactionId);
            
            alert('Transaction cancelled successfully!');
        });

        // Print transaction
        document.getElementById('printTransaction').addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            const transaction = transactionData[transactionId];
            
            // Populate print modal
            document.getElementById('printTransactionId').textContent = transaction.id;
            document.getElementById('printTransactionNo').textContent = transaction.transaction_no;
            document.getElementById('printDateCreated').textContent = formatDateTime(transaction.date_created);
            document.getElementById('printStatus').textContent = transaction.status;
            document.getElementById('printRequestingParty').textContent = transaction.requesting_party;
            document.getElementById('printContactNumber').textContent = transaction.contact_number;
            document.getElementById('printRelationship').textContent = transaction.relationship;
            document.getElementById('printAddress').textContent = transaction.address;
            document.getElementById('printDocumentType').textContent = transaction.document_type;
            document.getElementById('printPurpose').textContent = transaction.purpose;
            document.getElementById('printPaymentMode').textContent = transaction.payment_mode;
            
            // Populate certificate types
            const certificateTypesContainer = document.getElementById('printCertificateTypes');
            certificateTypesContainer.innerHTML = '';
            transaction.certificate_types.forEach(cert => {
                const certDiv = document.createElement('div');
                certDiv.className = 'paper-form-row';
                certDiv.innerHTML = `
                    <div class="paper-form-label">✓</div>
                    <div class="paper-form-value">${cert}</div>
                `;
                certificateTypesContainer.appendChild(certDiv);
            });
            
            // Show print modal
            const printModal = new bootstrap.Modal(document.getElementById('printModal'));
            printModal.show();
        });

        // Export functions
        document.getElementById('exportExcel').addEventListener('click', function() {
            alert('Excel export functionality would be implemented here');
        });

        document.getElementById('exportPDF').addEventListener('click', function() {
            alert('PDF export functionality would be implemented here');
        });

        document.getElementById('printData').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>