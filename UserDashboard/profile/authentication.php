<?php
session_start();
include __DIR__ . "/../../backend/db.php";

// Include PHPMailer (via Composer or manual include)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../vendor/autoload.php'; // Adjust path if needed

function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rtwsele_luna@gmail.com'; // Your SMTP email
        $mail->Password = '';    // Gmail App Password or SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@ccro.gov.ph', 'CCRO System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'CCRO System - Verification Code';
        $mail->Body = "<h3>Your OTP is:</h3><p style='font-size: 24px;'>$otp</p><p>This code will expire in 10 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

function generateOTP() {
    return sprintf('%06d', mt_rand(0, 999999));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    if ($action === 'send_otp') {
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($email) && empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Please provide either email or phone number.']);
            exit;
        }

        try {
            if (!empty($email)) {
                $stmt = $conn->prepare("SELECT id, fullname, email_address FROM customer WHERE email_address = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    echo json_encode(['success' => false, 'message' => 'Email not found in our system. Please register as a new user.']);
                    exit;
                }

                $contact_method = 'email';
                $contact_value = $email;
            } else {
                $stmt = $conn->prepare("SELECT id, fullname, contactno FROM customer WHERE contactno = ? LIMIT 1");
                $stmt->execute([$phone]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user) {
                    echo json_encode(['success' => false, 'message' => 'Phone number not found in our system. Please register as a new user.']);
                    exit;
                }

                $contact_method = 'phone';
                $contact_value = $phone;
            }

            $otp = generateOTP();
            $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $_SESSION['otp_data'] = [
                'otp' => $otp,
                'user_id' => $user['id'],
                'contact_method' => $contact_method,
                'contact_value' => $contact_value,
                'expires_at' => $expires_at,
                'attempts' => 0
            ];

            if ($contact_method === 'email') {
                $sent = sendOTPEmail($contact_value, $otp);
                $message = "Verification code sent to your email address.";
            } else {
                // Add SMS API integration here if needed
                error_log("SMS OTP for $contact_value: $otp");
                $sent = true;
                $message = "Verification code sent to your phone number.";
            }

            if ($sent) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send verification code. Please try again.']);
            }

        } catch (PDOException $e) {
            error_log("OTP send error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
        }
        exit;
    }

    if ($action === 'verify_otp') {
        $entered_otp = trim($_POST['otp'] ?? '');

        if (empty($entered_otp)) {
            echo json_encode(['success' => false, 'message' => 'Please enter the verification code.']);
            exit;
        }

        if (!isset($_SESSION['otp_data'])) {
            echo json_encode(['success' => false, 'message' => 'No verification code found. Please request a new one.']);
            exit;
        }

        $otp_data = $_SESSION['otp_data'];

        if (strtotime($otp_data['expires_at']) < time()) {
            unset($_SESSION['otp_data']);
            echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new one.']);
            exit;
        }

        if ($otp_data['attempts'] >= 3) {
            unset($_SESSION['otp_data']);
            echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Please request a new verification code.']);
            exit;
        }

        if ($entered_otp !== $otp_data['otp']) {
            $_SESSION['otp_data']['attempts']++;
            $remaining = 3 - $_SESSION['otp_data']['attempts'];
            echo json_encode(['success' => false, 'message' => "Invalid verification code. $remaining attempts remaining."]);
            exit;
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM customer WHERE id = ?");
            $stmt->execute([$otp_data['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $_SESSION['authenticated_user'] = [
                    'id' => $user['id'],
                    'fullname' => $user['fullname'],
                    'email_address' => $user['email_address'],
                    'contactno' => $user['contactno'],
                    'authenticated_at' => date('Y-m-d H:i:s')
                ];

                $_SESSION['user_email'] = $user['email_address'];
                unset($_SESSION['otp_data']);

                echo json_encode(['success' => true, 'message' => 'Authentication successful!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
            }

        } catch (PDOException $e) {
            error_log("OTP verify error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication - CCRO System</title>
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .auth-header p {
            color: #666;
            margin-bottom: 0;
        }

        .auth-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            border: none;
        }

        .auth-divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }

        .auth-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .auth-divider span {
            background: white;
            padding: 0 20px;
            color: #666;
            font-weight: 500;
        }

        .otp-input {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            text-decoration: none;
        }

        .certificate-badge {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .certificate-badge i {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        @media (max-width: 576px) {
            .auth-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .auth-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container animate__animated animate__fadeInUp">
        <!-- Back Link -->
        <a href="../certificatetype.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Back to Certificate Selection
        </a>

        <!-- Header -->
        <div class="auth-header">
            <div class="auth-icon animate__animated animate__bounceIn">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Returning User Authentication</h2>
            <p>Verify your identity to access your request history</p>
        </div>

        <!-- Certificate Type Badge -->
        <?php if (!empty($certificate_type)): ?>
            <div class="certificate-badge">
                <?php
                $icon = '';
                $name = '';
                switch($certificate_type) {
                    case 'marriage':
                        $icon = 'fas fa-ring';
                        $name = 'Marriage Certificate';
                        break;
                    case 'livebirth':
                        $icon = 'fas fa-baby';
                        $name = 'Birth Certificate';
                        break;
                    case 'death':
                        $icon = 'fas fa-cross';
                        $name = 'Death Certificate';
                        break;
                    default:
                        $icon = 'fas fa-certificate';
                        $name = 'Certificate';
                }
                ?>
                <i class="<?php echo $icon; ?>"></i>
                <div><strong>Selected: <?php echo htmlspecialchars($name); ?></strong></div>
            </div>
        <?php endif; ?>

        <!-- Step 1: Contact Information -->
        <div id="step1" class="step active">
            <form id="contactForm">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email">
                </div>

                <div class="auth-divider">
                    <span>OR</span>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone me-2"></i>Phone Number
                    </label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your registered phone number">
                </div>

                <div id="contactAlert"></div>

                <button type="submit" class="btn btn-primary" id="sendOtpBtn">
                    <span class="btn-text">Send Verification Code</span>
                    <div class="loading-spinner"></div>
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    We'll send a verification code to confirm your identity
                </small>
            </div>
        </div>

        <!-- Step 2: OTP Verification -->
        <div id="step2" class="step">
            <div class="text-center mb-4">
                <h5>Verification Code Sent</h5>
                <p class="text-muted" id="sentToText">Enter the 6-digit code sent to your contact method</p>
            </div>

            <form id="otpForm">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key me-2"></i>Verification Code
                    </label>
                    <input type="text" class="form-control otp-input" id="otp" name="otp" maxlength="6" placeholder="000000">
                </div>

                <div id="otpAlert"></div>

                <button type="submit" class="btn btn-primary" id="verifyOtpBtn">
                    <span class="btn-text">Verify Code</span>
                    <div class="loading-spinner"></div>
                </button>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary btn-sm" id="resendOtpBtn">
                        <i class="fas fa-redo me-1"></i>Resend Code
                    </button>
                </div>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Code expires in <span id="countdown">10:00</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        let countdownInterval;
        
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contactForm');
            const otpForm = document.getElementById('otpForm');
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const resendOtpBtn = document.getElementById('resendOtpBtn');
            
            // Handle contact form submission
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                
                if (!email && !phone) {
                    showAlert('contactAlert', 'danger', 'Please provide either email or phone number.');
                    return;
                }
                
                if (email && phone) {
                    showAlert('contactAlert', 'warning', 'Please provide only one contact method.');
                    return;
                }
                
                setButtonLoading(sendOtpBtn, true);
                
                const formData = new FormData();
                formData.append('action', 'send_otp');
                formData.append('email', email);
                formData.append('phone', phone);
                
                fetch('authentication.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setButtonLoading(sendOtpBtn, false);
                    
                    if (data.success) {
                        showAlert('contactAlert', 'success', data.message);
                        setTimeout(() => {
                            switchToStep2(email || phone);
                        }, 1500);
                    } else {
                        showAlert('contactAlert', 'danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setButtonLoading(sendOtpBtn, false);
                    showAlert('contactAlert', 'danger', 'Network error. Please try again.');
                });
            });
            
            // Handle OTP form submission
            otpForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const otp = document.getElementById('otp').value.trim();
                
                if (!otp || otp.length !== 6) {
                    showAlert('otpAlert', 'danger', 'Please enter a valid 6-digit code.');
                    return;
                }
                
                setButtonLoading(verifyOtpBtn, true);
                
                const formData = new FormData();
                formData.append('action', 'verify_otp');
                formData.append('otp', otp);
                
                fetch('authentication.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setButtonLoading(verifyOtpBtn, false);
                    
                    if (data.success) {
                        showAlert('otpAlert', 'success', data.message);
                        setTimeout(() => {
                            const certificateType = '<?php echo htmlspecialchars($certificate_type); ?>';
                            const redirectUrl = certificateType ? 
                                `returning.php?type=${encodeURIComponent(certificateType)}` : 
                                'returning.php';
                            window.location.href = redirectUrl;
                        }, 1500);
                    } else {
                        showAlert('otpAlert', 'danger', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    setButtonLoading(verifyOtpBtn, false);
                    showAlert('otpAlert', 'danger', 'Network error. Please try again.');
                });
            });
            
            // Handle resend OTP
            resendOtpBtn.addEventListener('click', function() {
                // Go back to step 1
                switchToStep1();
            });
            
            // OTP input formatting
            document.getElementById('otp').addEventListener('input', function(e) {
                // Remove non-digits
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        });
        
        function switchToStep2(contactValue) {
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
            
            // Update sent to text
            const isEmail = contactValue.includes('@');
            const maskedValue = isEmail ? 
                contactValue.replace(/(.{2})(.*)(@.*)/, '$1***$3') :
                contactValue.replace(/(.{3})(.*)(.{2})/, '$1***$3');
            
            document.getElementById('sentToText').textContent = 
                `Enter the 6-digit code sent to ${maskedValue}`;
            
            // Start countdown
            startCountdown(600); // 10 minutes
            
            // Focus on OTP input
            setTimeout(() => {
                document.getElementById('otp').focus();
            }, 100);
        }
        
        function switchToStep1() {
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
            
            // Clear countdown
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            // Clear forms
            document.getElementById('contactForm').reset();
            document.getElementById('otpForm').reset();
            document.getElementById('contactAlert').innerHTML = '';
            document.getElementById('otpAlert').innerHTML = '';
        }
        
        function startCountdown(seconds) {
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            const countdownElement = document.getElementById('countdown');
            
            countdownInterval = setInterval(() => {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                countdownElement.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
                
                if (seconds <= 0) {
                    clearInterval(countdownInterval);
                    showAlert('otpAlert', 'warning', 'Verification code has expired. Please request a new one.');
                    setTimeout(() => {
                        switchToStep1();
                    }, 3000);
                }
                
                seconds--;
            }, 1000);
        }
        
        function showAlert(containerId, type, message) {
            const container = document.getElementById(containerId);
            container.innerHTML = `
                <div class="alert alert-${type} animate__animated animate__fadeIn">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
            `;
        }
        
        function setButtonLoading(button, loading) {
            const spinner = button.querySelector('.loading-spinner');
            const text = button.querySelector('.btn-text');
            
            if (loading) {
                button.disabled = true;
                spinner.style.display = 'inline-block';
                text.style.display = 'none';
            } else {
                button.disabled = false;
                spinner.style.display = 'none';
                text.style.display = 'inline';
            }
        }
        
        console.log('🔐 Authentication system loaded');
    </script>
</body>
</html>