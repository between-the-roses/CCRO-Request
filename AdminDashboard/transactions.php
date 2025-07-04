<?php
// filepath: c:\xampp\htdocs\CCRO-Request\AdminDashboard\transactions.php
include 'includes/navbar.php'; 
include 'includes/sidebar.php';

// Initialize variables
$transactions = [];
$conn = null;

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$offset = ($page - 1) * $limit;

// Include database connection with error handling
try {
    // Check if the database connection file exists and include it
    $db_path = __DIR__ . '/../backend/db.php';
    if (file_exists($db_path)) {
        include $db_path;
    } else {
        // Alternative path check
        $alt_db_path = '../backend/db.php';
        if (file_exists($alt_db_path)) {
            include $alt_db_path;
        } else {
            throw new Exception("Database configuration file not found");
        }
    }

    // Check if connection is established
    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection not established");
    }

    // Test the connection
    if ($conn instanceof PDO) {
        $stmt = $conn->prepare("SELECT 1");
        $stmt->execute();
    } elseif ($conn instanceof mysqli) {
        if ($conn->connect_error) {
            throw new Exception("MySQLi connection failed: " . $conn->connect_error);
        }
    } else {
        throw new Exception("Unknown database connection type");
    }

    // Count total records
    $total_records = 0;
    if ($conn instanceof PDO) {
        $count_sql = "SELECT COUNT(*) as total 
                      FROM customer c 
                      LEFT JOIN transaction t ON c.customer_id = t.customer_id";
        $stmt = $conn->prepare($count_sql);
        $stmt->execute();
        $count_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_records = $count_result['total'];
    }

    // Calculate pagination values
    $total_pages = ceil($total_records / $limit);
    $start_record = $offset + 1;
    $end_record = min($offset + $limit, $total_records);

    // Fetch transactions based on connection type
    if ($conn instanceof PDO) {
        // PDO version - Using actual database schema from ERD with proper joins and correct column names
        $sql = "SELECT c.customer_id as id, 
                       CONCAT('TXN-', c.customer_id) as transaction_no,
                       c.fullname as requesting_party,
                       c.contactno as contact_number,
                       c.relationship,
                       c.address,
                       c.certificate_type as document_type,
                       'Cash' as payment_mode,
                       c.purpose,
                       COALESCE(t.status, 'pending') as status,
                       COALESCE(t.created_at, c.createdat) as date_created
                FROM customer c 
                LEFT JOIN transaction t ON c.customer_id = t.customer_id
                ORDER BY c.createdat DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } else {
        // MySQLi version - Using actual database schema from ERD with proper joins and correct column names
        $sql = "SELECT c.customer_id as id, 
                       CONCAT('TXN-', c.customer_id) as transaction_no,
                       c.fullname as requesting_party,
                       c.contactno as contact_number,
                       c.relationship,
                       c.address,
                       c.certificate_type as document_type,
                       'Cash' as payment_mode,
                       c.purpose,
                       COALESCE(t.status, 'pending') as status,
                       COALESCE(t.created_at, c.createdat) as date_created
                FROM customer c 
                LEFT JOIN transaction t ON c.customer_id = t.customer_id
                ORDER BY c.createdat DESC 
                LIMIT $limit OFFSET $offset";
        
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        } else {
            throw new Exception("Query failed: " . mysqli_error($conn));
        }
    }

    // Handle POST actions for updating transactions
    if (isset($_POST['action']) && isset($_POST['transaction_id'])) {
        $action = $_POST['action'];
        $transaction_id = $_POST['transaction_id'];
        $date_now = date('Y-m-d H:i:s');

        if ($conn instanceof PDO) {
            try {
                if ($action === 'confirm') {
                    $update_sql = "INSERT INTO transaction (customer_id, status, created_at) 
                                  VALUES (?, 'confirmed', ?) 
                                  ON DUPLICATE KEY UPDATE 
                                  status = 'confirmed'";
                    $stmt = $conn->prepare($update_sql);
                    $success = $stmt->execute([$transaction_id, $date_now]);
                } elseif ($action === 'cancel') {
                    $update_sql = "INSERT INTO transaction (customer_id, status, created_at) 
                                  VALUES (?, 'cancelled', ?) 
                                  ON DUPLICATE KEY UPDATE 
                                  status = 'cancelled'";
                    $stmt = $conn->prepare($update_sql);
                    $success = $stmt->execute([$transaction_id, $date_now]);
                } elseif ($action === 'update') {
                    $update_sql = "INSERT INTO transaction (customer_id, status, created_at) 
                                  VALUES (?, 'pending', ?) 
                                  ON DUPLICATE KEY UPDATE 
                                  created_at = ?";
                    $stmt = $conn->prepare($update_sql);
                    $success = $stmt->execute([$transaction_id, $date_now, $date_now]);
                }

                if ($success) {
                    header("Location: transactions.php?success=true&action=$action");
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Update error: " . $e->getMessage());
            }
        }
    }

    // Handle transaction detail view
    if (isset($_GET['id'])) {
        $transaction_id = $_GET['id'];
        
        if ($conn instanceof PDO) {
            $detail_sql = "SELECT c.*, 
                                  CONCAT('TXN-', c.customer_id) as transaction_no,
                                  t.status,
                                  'Cash' as payment_mode,
                                  COALESCE(t.created_at, c.createdat) as transaction_date
                           FROM customer c 
                           LEFT JOIN transaction t ON c.customer_id = t.customer_id
                           WHERE c.customer_id = ?";

            $stmt = $conn->prepare($detail_sql);
            $stmt->execute([$transaction_id]);
            $transaction_detail = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

} catch (Exception $e) {
    error_log("Database error in transactions.php: " . $e->getMessage());
    $transactions = [];
    $db_error = $e->getMessage();
    $total_records = 0;
    $total_pages = 1;
    $start_record = 0;
    $end_record = 0;
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

</head>
<body>
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
        
        <?php if (isset($db_error)): ?>
            <div class="error-message">
                <strong>Database Connection Error:</strong> <?php echo htmlspecialchars($db_error); ?>
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
                                        <span class="transaction-id" data-bs-toggle="modal" data-bs-target="#transactionModal" 
                                              data-id="<?php echo htmlspecialchars($transaction['id']); ?>"
                                              data-transaction='<?php echo htmlspecialchars(json_encode($transaction), ENT_QUOTES, 'UTF-8'); ?>'>
                                            <?php echo htmlspecialchars($transaction['id']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($transaction['transaction_no']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['requesting_party']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['relationship']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['document_type']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['payment_mode']); ?></td>
                                    <td>
                                        <?php 
                                        $status = $transaction['status'] ?? 'pending';
                                        if($status == 'confirmed'): 
                                        ?>
                                            <span class="status-confirmed">Confirmed</span>
                                        <?php elseif($status == 'cancelled'): ?>
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
                <div>
                    Showing <?php echo $start_record; ?> to <?php echo $end_record; ?> of <?php echo $total_records; ?> entries
                </div>
                <ul class="pagination">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>">Previous</a>
                    </li>
                    
                    <?php
                    // Calculate page range to display
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    // Show first page if not in range
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Show page numbers in range
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php 
                    endfor;
                    
                    // Show last page if not in range
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    
                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo min($total_pages, $page + 1); ?>">Next</a>
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
                                    <div class="certificate-checklist-title">Certificate Types Available</div>
                                    <div id="certificateTypes">
                                        <!-- Birth Certificate Options -->
                                        <div class="certificate-category" id="birthCertificates" style="display: none;">
                                            <div class="certificate-category-title">Birth Certificate Options:</div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="birth-photocopy" name="certificate[]" value="Certified Photocopy - Birth">
                                                <label for="birth-photocopy">Certified Photocopy</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="birth-form1a" name="certificate[]" value="Civil Registry Form No. 1A (Birth Available)">
                                                <label for="birth-form1a">Civil Registry Form No. 1A (Birth Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="birth-form1b" name="certificate[]" value="Civil Registry Form No. 1B (Birth - Not Available)">
                                                <label for="birth-form1b">Civil Registry Form No. 1B (Birth - Not Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="birth-form1c" name="certificate[]" value="Civil Registry Form No. 1C (Birth Destroyed)">
                                                <label for="birth-form1c">Civil Registry Form No. 1C (Birth Destroyed)</label>
                                            </div>
                                        </div>

                                        <!-- Marriage Certificate Options -->
                                        <div class="certificate-category" id="marriageCertificates" style="display: none;">
                                            <div class="certificate-category-title">Marriage Certificate Options:</div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="marriage-photocopy" name="certificate[]" value="Certified Photocopy - Marriage">
                                                <label for="marriage-photocopy">Certified Photocopy</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="marriage-form2a" name="certificate[]" value="Civil Registry Form No. 2A (Marriage Available)">
                                                <label for="marriage-form2a">Civil Registry Form No. 2A (Marriage Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="marriage-form2b" name="certificate[]" value="Civil Registry Form No. 2B (Marriage - Not Available)">
                                                <label for="marriage-form2b">Civil Registry Form No. 2B (Marriage - Not Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="marriage-form2c" name="certificate[]" value="Civil Registry Form No. 2C (Marriage Destroyed)">
                                                <label for="marriage-form2c">Civil Registry Form No. 2C (Marriage Destroyed)</label>
                                            </div>
                                        </div>

                                        <!-- Death Certificate Options -->
                                        <div class="certificate-category" id="deathCertificates" style="display: none;">
                                            <div class="certificate-category-title">Death Certificate Options:</div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="death-photocopy" name="certificate[]" value="Certified Photocopy - Death">
                                                <label for="death-photocopy">Certified Photocopy</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="death-form3a" name="certificate[]" value="Civil Registry Form No. 3A (Death Available)">
                                                <label for="death-form3a">Civil Registry Form No. 3A (Death Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="death-form3b" name="certificate[]" value="Civil Registry Form No. 3B (Death - Not Available)">
                                                <label for="death-form3b">Civil Registry Form No. 3B (Death - Not Available)</label>
                                            </div>
                                            <div class="certificate-item">
                                                <input type="checkbox" id="death-form3c" name="certificate[]" value="Civil Registry Form No. 3C (Death Destroyed)">
                                                <label for="death-form3c">Civil Registry Form No. 3C (Death Destroyed)</label>
                                            </div>
                                        </div>

                                        <!-- Other Certificate Types -->
                                        <div class="certificate-category" id="otherCertificates" style="display: none;">
                                            <div class="certificate-category-title">Other Certificate Types:</div>
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
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <button class="btn-confirm" id="confirmTransaction">
                                    <i class='bx bx-check'></i> Confirm
                                </button>
                                <button class="btn-cancel" id="cancelTransaction">
                                    <i class='bx bx-x'></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.transaction-id').forEach(id => {
                id.addEventListener('click', function() {
                    const transactionJson = this.getAttribute('data-transaction');
                    
                    try {
                        const transaction = JSON.parse(transactionJson);
                        loadTransactionDetails(transaction);
                    } catch (e) {
                        console.error('Error parsing transaction data:', e);
                    }
                });
            });
        });

        function loadTransactionDetails(transaction) {
            // Populate basic fields with real data
            document.getElementById('transactionId').value = transaction.id;
            document.getElementById('transactionNo').value = transaction.transaction_no;
            document.getElementById('requestingParty').value = transaction.requesting_party;
            document.getElementById('contactNumber').value = transaction.contact_number;
            document.getElementById('relationship').value = transaction.relationship;
            document.getElementById('address').value = transaction.address;
            document.getElementById('documentType').value = transaction.document_type;
            document.getElementById('paymentMode').value = transaction.payment_mode;
            document.getElementById('purpose').value = transaction.purpose;
            document.getElementById('dateCreated').value = formatDateTime(transaction.date_created);

            // Update status display
            const statusElement = document.getElementById('transactionStatus');
            statusElement.textContent = transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1);
            statusElement.className = `status-${transaction.status.toLowerCase()}`;

            // Show certificate types based on document type
            showCertificateTypes(transaction.document_type);

            // Create basic history
            const history = [
                {
                    action: 'Transaction Created',
                    timestamp: transaction.date_created,
                    staff: null,
                    notes: 'Initial transaction created'
                }
            ];

            if (transaction.status === 'confirmed') {
                history.push({
                    action: 'Transaction Confirmed',
                    timestamp: transaction.date_created,
                    staff: 'System Admin',
                    notes: 'Transaction confirmed'
                });
            } else if (transaction.status === 'cancelled') {
                history.push({
                    action: 'Transaction Cancelled',
                    timestamp: transaction.date_created,
                    staff: 'System Admin',
                    notes: 'Transaction cancelled'
                });
            }

            loadTransactionHistory(history);
        }

        function showCertificateTypes(documentType) {
            // Hide all certificate categories first
            const categories = document.querySelectorAll('.certificate-category');
            categories.forEach(category => {
                category.style.display = 'none';
            });

            // Clear all checkboxes
            const checkboxes = document.querySelectorAll('#certificateTypes input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                checkbox.disabled = false;
            });

            // Show appropriate category based on document type
            const docType = documentType.toLowerCase();
            
            if (docType.includes('birth') || docType.includes('livebirth')) {
                document.getElementById('birthCertificates').style.display = 'block';
                document.getElementById('birth-photocopy').checked = true;
            } else if (docType.includes('marriage')) {
                document.getElementById('marriageCertificates').style.display = 'block';
                document.getElementById('marriage-photocopy').checked = true;
            } else if (docType.includes('death')) {
                document.getElementById('deathCertificates').style.display = 'block';
                document.getElementById('death-photocopy').checked = true;
            } else {
                document.getElementById('otherCertificates').style.display = 'block';
                if (docType.includes('cenomar')) {
                    document.getElementById('cert-cenomar').checked = true;
                } else if (docType.includes('fsis')) {
                    document.getElementById('cert-fsis').checked = true;
                }
            }
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

        // Handle confirm/cancel actions
        document.getElementById('confirmTransaction').addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="confirm">
                <input type="hidden" name="transaction_id" value="${transactionId}">
            `;
            document.body.appendChild(form);
            form.submit();
        });

        document.getElementById('cancelTransaction').addEventListener('click', function() {
            const transactionId = document.getElementById('transactionId').value;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="cancel">
                <input type="hidden" name="transaction_id" value="${transactionId}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    </script>

    <style>
        body {
            background-color: #f5f6fa;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-content {
            margin-left: 265px;
            margin-top: 5px;
            padding: 1px;
            transition: all 0.3s ease;
            min-height: calc(100vh - 85px);
        }
        
        .main-content.expanded {
            margin-left: 60px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-top: 20px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
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
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
            margin-top: 0px;
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
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
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
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
        
        .certificate-category {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            background-color: #fff;
        }
        
        .certificate-category-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .certificate-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .certificate-item input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.1);
        }
        
        .certificate-item label {
            margin-bottom: 0;
            cursor: pointer;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .certificate-item:last-child {
            margin-bottom: 0;
        }
        
        .certificate-item:hover {
            background-color: #f8f9fa;
            border-radius: 3px;
            margin-left: -5px;
            margin-right: -5px;
            padding-left: 5px;
            padding-right: 5px;
        }
        
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
        
        .transaction-id {
            color: #3498db;
            text-decoration: underline;
            cursor: pointer;
        }
        
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
        
        @media (max-width: 768px) {
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
    </style>
</body>
</html>