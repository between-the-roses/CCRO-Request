<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\profile\returning.php
session_start();
include __DIR__ . "/../../backend/db.php";

// // Check if user is logged in (you can adjust this based on your authentication system)
// if (!isset($_SESSION['user_email']) && !isset($_SESSION['customer_data'])) {
//     //header("Location: ../customer.php");
//     exit;
// }

// Get user email from session
$user_email = $_SESSION['user_email'] ?? $_SESSION['customer_data']['email_address'] ?? '';

if (empty($user_email)) {
    $error_message = "No user email found. Please log in again.";
    $request_history = [];
} else {
    try {
        // Fetch user's request history from the database
        $stmt = $conn->prepare("
            SELECT 
                c.id,
                c.fullname,
                c.certificate_type,
                c.copies,
                c.created_at,
                c.status,
                CASE 
                    WHEN c.certificate_type = 'marriage' THEN m.bridename || ' & ' || m.groomname
                    WHEN c.certificate_type = 'livebirth' THEN lb.firstname || ' ' || COALESCE(lb.middlename, '') || ' ' || lb.lastname
                    WHEN c.certificate_type = 'death' THEN d.deceasedname
                    ELSE 'N/A'
                END as certificate_details,
                t.transaction_number
            FROM customer c
            LEFT JOIN marriage m ON c.id = m.customer_id AND c.certificate_type = 'marriage'
            LEFT JOIN livebirth lb ON c.id = lb.customer_id AND c.certificate_type = 'livebirth'
            LEFT JOIN death d ON c.id = d.customer_id AND c.certificate_type = 'death'
            LEFT JOIN (
                SELECT customer_id, transaction_number 
                FROM transaction 
                WHERE transaction_number IS NOT NULL
            ) t ON c.id = t.customer_id
            WHERE c.email_address = ?
            ORDER BY c.created_at DESC
        ");
        
        $stmt->execute([$user_email]);
        $request_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database error in returning.php: " . $e->getMessage());
        $error_message = "Unable to retrieve request history. Please try again later.";
        $request_history = [];
    }
}

// Function to format certificate type
function formatCertificateType($type) {
    return match($type) {
        'marriage' => 'Marriage Certificate',
        'livebirth' => 'Birth Certificate',
        'death' => 'Death Certificate',
        default => ucfirst($type) . ' Certificate',
    };
}

// Function to format status
function formatStatus($status) {
    return match(strtolower($status)) {
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'processing' => '<span class="badge bg-info">Processing</span>',
        'ready' => '<span class="badge bg-success">Ready</span>',
        'completed' => '<span class="badge bg-primary">Completed</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        default => '<span class="badge bg-secondary">Unknown</span>',
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request History - CCRO System</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            margin-bottom: 0;
        }

        .profile-content {
            background: white;
            border-radius: 0 0 20px 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .welcome-section {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .history-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }

        .table td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f3f4;
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .certificate-details {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .btn-new-request {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }

        .btn-new-request:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }

        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .transaction-number {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.85rem;
        }

        .date-display {
            font-size: 0.9rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 0 10px;
            }
            
            .profile-content {
                padding: 20px;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
            background-color: #f0f9ff;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 30px;
        }

        @media (max-width: 576px) {
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Profile Header -->
        <div class="profile-header animate__animated animate__fadeInDown">
            <h1><i class="fas fa-user-circle me-3"></i>Welcome Back!</h1>
            <p class="mb-0">Your Certificate Request History</p>
        </div>

        <!-- Profile Content -->
        <div class="profile-content animate__animated animate__fadeInUp">
            
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h4><i class="fas fa-hand-wave me-2"></i>Hello, <?php echo htmlspecialchars(explode('@', $user_email)[0]); ?>!</h4>
                <p class="mb-0">Here's a summary of your certificate requests. You can view your request history and submit new requests anytime.</p>
            </div>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php else: ?>
                
                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card animate__animated animate__zoomIn">
                        <div class="stat-number"><?php echo count($request_history); ?></div>
                        <div class="stat-label">Total Requests</div>
                    </div>
                    <div class="stat-card animate__animated animate__zoomIn animate__delay-1s">
                        <div class="stat-number">
                            <?php 
                            $pending_count = count(array_filter($request_history, 
                                fn($req) => strtolower($req['status'] ?? 'pending') === 'pending'
                            ));
                            echo $pending_count;
                            ?>
                        </div>
                        <div class="stat-label">Pending Requests</div>
                    </div>
                    <div class="stat-card animate__animated animate__zoomIn animate__delay-2s">
                        <div class="stat-number">
                            <?php 
                            $completed_count = count(array_filter($request_history, 
                                fn($req) => in_array(strtolower($req['status'] ?? ''), ['completed', 'ready'])
                            ));
                            echo $completed_count;
                            ?>
                        </div>
                        <div class="stat-label">Completed Requests</div>
                    </div>
                </div>

                <!-- Request History Table -->
                <h4 class="mb-4"><i class="fas fa-history me-2"></i>Request History</h4>
                
                <?php if (empty($request_history)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No Request History Found</h5>
                        <p>You haven't submitted any certificate requests yet. Start by requesting your first certificate!</p>
                    </div>
                <?php else: ?>
                    <div class="history-table">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag me-1"></i>Request ID</th>
                                        <th><i class="fas fa-certificate me-1"></i>Certificate Type</th>
                                        <th><i class="fas fa-info-circle me-1"></i>Details</th>
                                        <th><i class="fas fa-copy me-1"></i>Copies</th>
                                        <th><i class="fas fa-calendar me-1"></i>Date Requested</th>
                                        <th><i class="fas fa-tasks me-1"></i>Status</th>
                                        <th><i class="fas fa-receipt me-1"></i>Transaction #</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($request_history as $request): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo htmlspecialchars($request['id']); ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $icon = '';
                                                    switch($request['certificate_type']) {
                                                        case 'marriage': $icon = 'fas fa-ring'; break;
                                                        case 'livebirth': $icon = 'fas fa-baby'; break;
                                                        case 'death': $icon = 'fas fa-cross'; break;
                                                        default: $icon = 'fas fa-certificate';
                                                    }
                                                    ?>
                                                    <i class="<?php echo $icon; ?> me-2 text-primary"></i>
                                                    <?php echo formatCertificateType($request['certificate_type']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="certificate-details">
                                                    <?php echo htmlspecialchars($request['certificate_details'] ?? 'N/A'); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo htmlspecialchars($request['copies']); ?> copy(s)
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-display">
                                                    <?php 
                                                    $date = new DateTime($request['created_at']);
                                                    echo $date->format('M d, Y'); 
                                                    ?>
                                                    <br>
                                                    <small><?php echo $date->format('h:i A'); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo formatStatus($request['status'] ?? 'pending'); ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($request['transaction_number'])): ?>
                                                    <span class="transaction-number">
                                                        <?php echo htmlspecialchars($request['transaction_number']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="../certificatetype.php" class="btn btn-new-request animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-plus-circle me-2"></i>Request New Certificate
                </a>
                <a href="http://localhost/CCRO-Request/index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            <!-- Additional Info -->
            <div class="alert alert-info mt-4">
                <h6><i class="fas fa-info-circle me-2"></i>Need Help?</h6>
                <p class="mb-0">
                    If you have questions about your requests or need assistance, please contact the Civil Registrar's Office 
                    or use the status tracker to check your request progress.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for better UX
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });

            // Add click tracking for new request button
            const newRequestBtn = document.querySelector('.btn-new-request');
            if (newRequestBtn) {
                newRequestBtn.addEventListener('click', function() {
                    console.log('🚀 User requesting new certificate');
                    
                    // Optional: Store user preference or analytics
                    sessionStorage.setItem('returning_user', 'true');
                    sessionStorage.setItem('last_visit', new Date().toISOString());
                });
            }

            // Animate stats on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__bounceIn');
                    }
                });
            });

            document.querySelectorAll('.stat-card').forEach(card => {
                observer.observe(card);
            });

            // Add tooltips for transaction numbers
            const transactionNumbers = document.querySelectorAll('.transaction-number');
            transactionNumbers.forEach(tn => {
                tn.setAttribute('title', 'Transaction Number - Use this to track your request');
                new bootstrap.Tooltip(tn);
            });

            console.log('✅ Returning user profile loaded successfully');
        });

        // Function to refresh request status (optional)
        function refreshStatus() {
            location.reload();
        }

        // Auto-refresh every 5 minutes for status updates (optional)
        setInterval(refreshStatus, 300000);
    </script>
</body>
</html>