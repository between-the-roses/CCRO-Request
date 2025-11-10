<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\tracker.php
session_start();
include __DIR__ . "/../backend/db.php";

// ✅ DEBUG: Check if connection exists
if (!isset($conn)) {
    die("ERROR: Database connection not established. Check db.php file.");
}

$verification_result = null;
$error_message = null;
$success_message = null;
$otp_sent = false;
$show_otp_form = false;
$search_mode = $_POST['mode'] ?? $_SESSION['search_mode'] ?? 'transaction';

// Handle OTP generation and sending (mock implementation)
function generateOTP() {
    return str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
}

function sendOTP($contact, $otp, $method) {
    // Mock OTP sending - in production, integrate with SMS/Email service
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expires'] = time() + 300; // 5 minutes
    $_SESSION['otp_contact'] = $contact;
    $_SESSION['otp_method'] = $method;
    
    // For testing - log the OTP
    error_log("OTP sent to $contact: $otp");
    
    return true;
}

// ✅ FIXED: Extract transaction_id from transaction number
function extractTransactionIdFromNumber($transaction_number) {
    if (preg_match('/^CCR-(\d+)$/', $transaction_number, $matches)) {
        return intval($matches[1]);
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'switch_mode') {
        $search_mode = $_POST['mode'] ?? 'transaction';
        $_SESSION['search_mode'] = $search_mode;
        // Clear any existing OTP session data when switching modes
        unset($_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['pending_results'], $_SESSION['search_details'], $_SESSION['otp_contact'], $_SESSION['otp_method']);
        
    } elseif ($action === 'track_transaction') {
        // Transaction number tracking
        $transaction_number = strtoupper(trim($_POST['transaction_no'] ?? ''));
        
        if (empty($transaction_number)) {
            $error_message = "Transaction number is required.";
        } else {
            // ✅ DEBUG: Log the query
            error_log("Searching for transaction: " . $transaction_number);
            
            try {
                $stmt = $conn->prepare("
                    SELECT 
                        t.transaction_id,
                        t.transaction_no,
                        t.customer_id,
                        t.transaction_status,
                        t.transactiontype,
                        t.created_at,
                        t.updated_at,
                        c.fullname,
                        c.email_address,
                        c.contactno,
                        c.certificate_type,
                        c.copies,
                        c.purpose
                    FROM transaction t
                    JOIN customer c ON t.customer_id = c.customer_id
                    WHERE t.transaction_no = ?
                ");
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . print_r($conn->errorInfo(), true));
                }
                
                if (!$stmt->execute([$transaction_number])) {
                    throw new Exception("Execute failed: " . print_r($stmt->errorInfo(), true));
                }
                
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("Found " . count($results) . " results");
                
                if (empty($results)) {
                    $error_message = "No records found for: " . htmlspecialchars($transaction_number);
                } else {
                    $verification_result = $results;
                    $success_message = "Transaction found successfully!";
                }
                
            } catch (PDOException $e) {
                // ✅ SHOW REAL ERROR
                $error_message = "Database Error: " . $e->getMessage();
                error_log("PDOException: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                
            } catch (Exception $e) {
                // ✅ SHOW REAL ERROR
                $error_message = "Error: " . $e->getMessage();
                error_log("Exception: " . $e->getMessage());
            }
        }
        
    } elseif ($action === 'search_details') {
        // Search by personal details
        $search_mode = 'details';
        $_SESSION['search_mode'] = $search_mode;
        
        $fullname = trim($_POST['fullname'] ?? '');
        $contact_method = $_POST['contact_method'] ?? 'email';
        $contact_value = trim($_POST['contact_value'] ?? '');
        
        if (empty($fullname) || empty($contact_value)) {
            $error_message = "All fields are required for identity verification.";
        } else {
            try {
                // ✅ FIXED: Use parameterized query with correct column names
                $contact_field = $contact_method === 'email' ? 'c.email_address' : 'c.contactno';
                $stmt = $conn->prepare("
                    SELECT 
                        t.transaction_id,
                        t.transaction_no,
                        t.customer_id,
                        t.status,
                        c.fullname,
                        c.email_address,
                        c.contactno,
                        c.certificate_type,
                        c.created_at
                    FROM transaction t
                    JOIN customer c ON t.customer_id = c.customer_id
                    WHERE LOWER(c.fullname) = LOWER(?) 
                    AND $contact_field = ?
                    ORDER BY c.created_at DESC
                ");
                
                $stmt->execute([$fullname, $contact_value]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($results)) {
                    $error_message = "No records found matching your information. Please check your name and contact details.";
                } else {
                    // Generate and send OTP
                    $otp = generateOTP();
                    if (sendOTP($contact_value, $otp, $contact_method)) {
                        $otp_sent = true;
                        $show_otp_form = true;
                        $_SESSION['pending_results'] = $results;
                        $_SESSION['search_details'] = $_POST;
                        $success_message = "Verification code sent successfully to " . htmlspecialchars($contact_value);
                    } else {
                        $error_message = "Failed to send verification code. Please try again.";
                    }
                }
                
            } catch (PDOException $e) {
                $error_message = "Database error occurred. Please try again later.";
                error_log("Database error: " . $e->getMessage());
            }
        }
        
    } elseif ($action === 'verify_otp') {
        // Verify OTP
        $search_mode = 'details';
        $_SESSION['search_mode'] = $search_mode;
        
        $entered_otp = trim($_POST['otp'] ?? '');
        $stored_otp = $_SESSION['otp'] ?? '';
        $otp_expires = $_SESSION['otp_expires'] ?? 0;
        
        if (empty($entered_otp)) {
            $error_message = "Please enter the verification code.";
            $show_otp_form = true;
        } elseif (time() > $otp_expires) {
            $error_message = "Verification code has expired. Please request a new one.";
            // Clear expired OTP
            unset($_SESSION['otp'], $_SESSION['otp_expires']);
            $show_otp_form = false;
        } elseif ($entered_otp !== $stored_otp) {
            $error_message = "Invalid verification code. Please try again.";
            $show_otp_form = true;
        } else {
            // OTP verified successfully
            if (isset($_SESSION['pending_results'])) {
                $transaction_ids = array_column($_SESSION['pending_results'], 'transaction_id');
                
                try {
                    // ✅ FIXED: Get full details using transaction_id
                    $placeholders = str_repeat('?,', count($transaction_ids) - 1) . '?';
                    $stmt = $conn->prepare("
                        SELECT 
                            t.transaction_id,
                            t.transaction_no,
                            t.customer_id,
                            t.status,
                            t.transactiontype,
                            t.created_at,
                            c.fullname,
                            c.email_address,
                            c.contactno,
                            c.certificate_type,
                            c.copies,
                            c.purpose,
                            CASE 
                                WHEN c.certificate_type = 'marriage' THEN m.husbandname
                                WHEN c.certificate_type = 'livebirth' THEN b.childinfo
                                WHEN c.certificate_type = 'death' THEN d.deceasedname
                                ELSE 'N/A'
                            END as certificate_name,
                            COALESCE(m.wifename, '') as spouse_name,
                            CASE 
                                WHEN c.certificate_type = 'marriage' THEN m.marriagedate
                                WHEN c.certificate_type = 'livebirth' THEN b.birthdate
                                WHEN c.certificate_type = 'death' THEN d.deathdate
                                ELSE NULL
                            END as certificate_date,
                            CASE 
                                WHEN c.certificate_type = 'marriage' THEN m.marriageplace
                                WHEN c.certificate_type = 'livebirth' THEN b.birthplace
                                WHEN c.certificate_type = 'death' THEN d.deathplace
                                ELSE NULL
                            END as certificate_place
                        FROM transaction t
                        JOIN customer c ON t.customer_id = c.customer_id
                        LEFT JOIN marriage m ON c.customer_id = m.customer_id AND c.certificate_type = 'marriage'
                        LEFT JOIN birth b ON c.customer_id = b.customer_id AND c.certificate_type = 'livebirth'
                        LEFT JOIN death d ON c.customer_id = d.customer_id AND c.certificate_type = 'death'
                        WHERE t.transaction_id IN ($placeholders)
                        ORDER BY t.created_at DESC
                    ");
                    
                    $stmt->execute($transaction_ids);
                    $verification_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $success_message = "Identity verified successfully! Found " . count($verification_result) . " request(s).";
                    
                    // Clear OTP session data
                    unset($_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['pending_results'], $_SESSION['search_details'], $_SESSION['otp_contact'], $_SESSION['otp_method']);
                    
                } catch (PDOException $e) {
                    $error_message = "Database error occurred while retrieving your records.";
                    error_log("Database error: " . $e->getMessage());
                }
            } else {
                $error_message = "Session expired. Please start the verification process again.";
            }
        }
        
    } elseif ($action === 'resend_otp') {
        // Resend OTP
        $search_mode = 'details';
        $_SESSION['search_mode'] = $search_mode;
        
        if (isset($_SESSION['otp_contact']) && isset($_SESSION['otp_method'])) {
            $otp = generateOTP();
            if (sendOTP($_SESSION['otp_contact'], $otp, $_SESSION['otp_method'])) {
                $otp_sent = true;
                $show_otp_form = true;
                $success_message = "New verification code sent to " . htmlspecialchars($_SESSION['otp_contact']);
                $error_message = null;
            } else {
                $error_message = "Failed to resend verification code.";
                $show_otp_form = true;
            }
        } else {
            $error_message = "Session expired. Please start the verification process again.";
        }
    }
}

// Check if we should show OTP form
if (isset($_SESSION['otp']) && isset($_SESSION['pending_results']) && !$verification_result && !$error_message) {
    $show_otp_form = true;
    $search_mode = 'details';
}

// Get status based on processing stage
function getRequestStatus($created_at, $status) {
    // ✅ FIXED: Use database status field instead of calculating from date
    $status_map = [
        'pending' => ['status' => 'Pending', 'color' => 'warning', 'icon' => 'clock'],
        //'on_review' => ['status' => 'Under Review', 'color' => 'info', 'icon' => 'search'],
        'approved' => ['status' => 'Processing', 'color' => 'primary', 'icon' => 'cog'],
        'completed' => ['status' => 'Ready for Pickup', 'color' => 'success', 'icon' => 'check-circle'],
        'cancelled' => ['status' => 'Cancelled', 'color' => 'danger', 'icon' => 'times-circle']
    ];
    
    return $status_map[strtolower($status)] ?? ['status' => 'Unknown', 'color' => 'secondary', 'icon' => 'question'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 90px;
        }
        
        .tracker-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .tracker-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .tracker-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .tracker-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .tracker-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .search-methods {
            padding: 2rem 2rem 1rem 2rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .method-toggle {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .method-btn {
            flex: 1;
            max-width: 200px;
            padding: 12px 24px;
            border: 2px solid #dee2e6;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .method-btn.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #007bff;
        }
        
        .method-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-form {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .transaction-input {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 1.2rem;
            padding: 1.5rem 1rem;
        }
        
        .btn-track {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            padding: 12px 40px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,123,255,0.3);
        }
        
        .otp-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        
        .otp-input {
            font-family: 'Courier New', monospace;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 8px;
            text-align: center;
            max-width: 300px;
            margin: 1rem auto;
        }
        
        .results-section {
            padding: 2rem;
            border-top: 1px solid #dee2e6;
        }
        
        .request-card {
            border: 1px solid #dee2e6;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .request-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--status-color);
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .transaction-number {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 2px;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            position: relative;
        }
        
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .progress-step {
            background: white;
            border: 4px solid #e9ecef;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            font-size: 0.8rem;
        }
        
        .progress-step.completed {
            border-color: #28a745;
            background: #28a745;
            color: white;
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
        }
        
        .info-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .priority-notice {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid #ffc107;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .transaction-example {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        
        .otp-debug {
            background: #e8f5e8;
            border: 1px solid #28a745;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .method-toggle {
                flex-direction: column;
            }
            
            .tracker-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="tracker-container">
        <div class="tracker-card">
            <div class="tracker-header">
                <h1><i class="fas fa-search me-3"></i>Document Status Tracker</h1>
                <p>Track your certificate request using your transaction number or personal details</p>
            </div>
            
            <div class="search-methods">
                <div class="priority-notice">
                    <h6><i class="fas fa-star me-2"></i>Quick Access</h6>
                    <p class="mb-0">
                        <strong>Have your transaction number?</strong> Use it for instant access to your request status.
                        <br><strong>No transaction number?</strong> Use your personal details with email/SMS verification.
                    </p>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="switch_mode">
                    <div class="method-toggle">
                        <button type="submit" name="mode" value="transaction" 
                                class="method-btn <?php echo $search_mode === 'transaction' ? 'active' : ''; ?>">
                            <i class="fas fa-receipt me-2"></i>
                            <div><strong>Transaction Number</strong></div>
                            <small>Fastest method</small>
                        </button>
                        <button type="submit" name="mode" value="details" 
                                class="method-btn <?php echo $search_mode === 'details' ? 'active' : ''; ?>">
                            <i class="fas fa-user-check me-2"></i>
                            <div><strong>Personal Details</strong></div>
                            <small>With verification</small>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="search-form">
                <?php if ($search_mode === 'transaction'): ?>
                    <!-- Transaction Number Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="track_transaction">
                        
                        <div class="text-center mb-4">
                            <h5><i class="fas fa-receipt me-2"></i>Enter Your Transaction Number</h5>
                            <p class="text-muted">Enter the transaction number you received from your request confirmation</p>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control transaction-input" id="transaction_number" 
                                           name="transaction_no"  
                                           placeholder="CCR-XX" 
                                           pattern="CCR-\d+" title="Format: CCR-XX" required>
                                    <label for="transaction_number">
                                        <i class="fas fa-receipt me-2"></i>Transaction Number
                                    </label>
                                </div>
                                
                                <div class="transaction-example">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Format Examples:</strong> CCR-98, CCR-99, CCR-100<br>
                                        Your transaction number was provided when you submitted your request.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-track">
                                <i class="fas fa-search me-2"></i>Track Request
                            </button>
                        </div>
                    </form>
                    
                <?php elseif ($search_mode === 'details' && !$show_otp_form): ?>
                    <!-- Personal Details Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="search_details">
                        
                        <div class="text-center mb-4">
                            <h5><i class="fas fa-user-check me-2"></i>Verify Your Identity</h5>
                            <p class="text-muted">We'll send a verification code to confirm your identity</p>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="fullname" name="fullname" 
                                           placeholder="Full Name" value="<?php echo htmlspecialchars($_POST['fullname'] ?? $_SESSION['search_details']['fullname'] ?? ''); ?>" required>
                                    <label for="fullname"><i class="fas fa-user me-2"></i>Full Name (as registered)</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="contact_method" name="contact_method" required>
                                        <option value="email" <?php echo ($_POST['contact_method'] ?? $_SESSION['search_details']['contact_method'] ?? '') === 'email' ? 'selected' : ''; ?>>Email</option>
                                        <option value="phone" <?php echo ($_POST['contact_method'] ?? $_SESSION['search_details']['contact_method'] ?? '') === 'phone' ? 'selected' : ''; ?>>Phone</option>
                                    </select>
                                    <label for="contact_method"><i class="fas fa-cog me-2"></i>Verify Via</label>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="contact_value" name="contact_value" 
                                           placeholder="Email or Phone" value="<?php echo htmlspecialchars($_POST['contact_value'] ?? $_SESSION['search_details']['contact_value'] ?? ''); ?>" required>
                                    <label for="contact_value"><i class="fas fa-envelope me-2"></i>Email or Phone Number</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-track">
                                <i class="fas fa-shield-alt me-2"></i>Send Verification Code
                            </button>
                        </div>
                    </form>
                    
                <?php elseif ($show_otp_form): ?>
                    <!-- OTP Verification Form -->
                    <div class="otp-section">
                        <h5><i class="fas fa-shield-alt me-2"></i>Verify Your Identity</h5>
                        <p class="mb-3">
                            We sent a 6-digit verification code to:<br>
                            <strong><?php echo htmlspecialchars($_SESSION['otp_contact'] ?? ''); ?></strong>
                        </p>
                        
                        <?php if (isset($_SESSION['otp'])): ?>
                            <div class="otp-debug">
                                <i class="fas fa-code me-2"></i>
                                <strong>For testing purposes, your verification code is: <?php echo $_SESSION['otp']; ?></strong>
                                <br><small>This message will be removed in production</small>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="verify_otp">
                            
                            <div class="form-floating">
                                <input type="text" class="form-control otp-input" id="otp" name="otp" 
                                       placeholder="000000" maxlength="6" pattern="\d{6}" autocomplete="off" required>
                                <label for="otp">Enter 6-digit code</label>
                            </div>
                            
                            <div class="d-flex gap-2 justify-content-center mt-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Verify Code
                                </button>
                            </div>
                        </form>
                        
                        <form method="POST" action="" class="mt-3">
                            <input type="hidden" name="action" value="resend_otp">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Resend Code
                            </button>
                        </form>
                        
                        <p class="text-muted mt-3">
                            <small>
                                Code expires in 5 minutes.<br>
                                Time remaining: <span id="countdown"></span>
                            </small>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($error_message): ?>
                <div class="results-section">
                    <div class="alert alert-danger alert-custom">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="results-section">
                    <div class="alert alert-success alert-custom">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($verification_result): ?>
                <div class="results-section">
                    <?php foreach ($verification_result as $request): ?>
                        <?php 
                        $status_info = getRequestStatus($request['created_at'], $request['transaction_status']);
                        ?>
                        <div class="request-card" style="--status-color: var(--bs-<?php echo $status_info['color']; ?>);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="fas fa-certificate me-2"></i>
                                        <?php echo ucfirst($request['transactiontype']); ?> Certificate Request
                                    </h5>
                                    <p class="transaction-number mb-0"><?php echo htmlspecialchars($request['transaction_no']); ?></p>
                                    <small class="text-muted">Transaction ID: <?php echo $request['transaction_id']; ?></small>
                                </div>
                                <span class="badge bg-<?php echo $status_info['color']; ?> status-badge">
                                    <i class="fas fa-<?php echo $status_info['icon']; ?> me-1"></i>
                                    <?php echo $status_info['status']; ?>
                                </span>
                            </div>
                            
                            <div class="progress-steps">
                                <div class="progress-step completed" title="Submitted">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="progress-step <?php echo in_array($status_info['status'], ['Processing', 'Ready for Pickup']) ? 'completed' : ''; ?>" title="Pending">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="progress-step <?php echo in_array($status_info['status'], ['Processing', 'Ready for Pickup']) ? 'completed' : ''; ?>" title="Processing">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="progress-step <?php echo $status_info['status'] === 'Completed' ? 'completed' : ''; ?>" title="Ready">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">Requester Name</div>
                                    <div class="info-value"><?php echo htmlspecialchars($request['fullname'] ?? 'N/A'); ?></div>
                                </div>
                                <?php if (!empty($request['spouse_name'])): ?>
                                    <div class="info-item">
                                        <div class="info-label">Spouse Name</div>
                                        <div class="info-value"><?php echo htmlspecialchars($request['spouse_name']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <div class="info-label">Copies Requested</div>
                                    <div class="info-value"><?php echo $request['copies']; ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Purpose</div>
                                    <div class="info-value"><?php echo htmlspecialchars($request['purpose']); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Request Date</div>
                                    <div class="info-value"><?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></div>
                                </div>
                                <!-- <?php if ($request['created_at']): ?>
                                    <div class="info-item">
                                        <div class="info-label">Certificate Date</div>
                                        <div class="info-value"><?php echo date('M j, Y', strtotime($request['created_at'])); ?></div>
                                    </div>
                                <?php endif; ?> -->
                            </div>
                            
                            <?php if ($status_info['status'] === 'Ready for Pickup'): ?>
                                <div class="alert alert-info alert-custom mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Ready for Pickup!</strong> Your certificate is ready. Please bring a valid ID and this transaction number (<?php echo htmlspecialchars($request['transaction_no']); ?>) to our office.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center">
            <a href="../index.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format transaction number input
        const transactionInput = document.getElementById('transaction_number');
        if (transactionInput) {
            transactionInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                value = value.replace(/[^A-Z0-9-]/g, '');
                
                if (!value.startsWith('CCR-') && value.length > 0) {
                    if (value.startsWith('CCR')) {
                        value = 'CCR-' + value.substring(3);
                    } else {
                        value = 'CCR-' + value;
                    }
                }
                
                e.target.value = value;
            });
        }
        
        // Auto-format OTP input
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
            
            // Auto-focus OTP input
            otpInput.focus();
        }
        
        // Auto-switch contact value placeholder based on method
        const contactMethod = document.getElementById('contact_method');
        const contactValue = document.getElementById('contact_value');
        
        if (contactMethod && contactValue) {
            contactMethod.addEventListener('change', function() {
                if (this.value === 'email') {
                    contactValue.placeholder = 'Enter your email address';
                    contactValue.type = 'email';
                } else {
                    contactValue.placeholder = 'Enter your phone number';
                    contactValue.type = 'tel';
                }
            });
        }
        
        // Countdown timer for OTP expiry
        <?php if (isset($_SESSION['otp_expires'])): ?>
        const expiryTime = <?php echo $_SESSION['otp_expires']; ?>;
        const countdownElement = document.getElementById('countdown');
        
        if (countdownElement) {
            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                const remaining = expiryTime - now;
                
                if (remaining <= 0) {
                    countdownElement.textContent = 'Expired';
                    countdownElement.className = 'text-danger';
                } else {
                    const minutes = Math.floor(remaining / 60);
                    const seconds = remaining % 60;
                    countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
        <?php endif; ?>
    </script>
</body>
</html>