<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/../backend/db.php";

if (!$conn) {
  echo "<div class='alert alert-danger mt-3'>Database connection failed.</div>";
  exit;
}

// Get certificate type - prioritize POST (from form submission), then GET (from URL), then session
$certificate_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['certificate_type'])) {
    // Form submission - use the hidden field value
    $certificate_type = trim($_POST['certificate_type']);
} elseif (isset($_GET['type'])) {
    // Coming from URL parameter (fresh selection)
    $certificate_type = trim($_GET['type']);
} elseif (isset($_SESSION['certificate_type'])) {
    // Use session value as fallback
    $certificate_type = $_SESSION['certificate_type'];
}

// Validate certificate type
if (!in_array($certificate_type, ['marriage', 'livebirth', 'death'])) {
    $certificate_type = 'marriage'; // Default fallback
}

// Always update session with current certificate type
$_SESSION['certificate_type'] = $certificate_type;

// Set page title based on certificate type
$page_titles = [
    'marriage' => 'Marriage Certificate Request',
    'livebirth' => 'Birth Certificate Request', 
    'death' => 'Death Certificate Request'
];
$page_title = $page_titles[$certificate_type] ?? 'Certificate Request';

// Form validation and processing
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = [
        'fullname' => 'Full Name',
        'email_address' => 'Email Address',
        'contactno' => 'Contact Number',
        'relationship' => 'Relationship to Document Owner',
        'civilstatus' => 'Civil Status',
        'purpose' => 'Purpose of Certification',
        'address' => 'Home Address'
    ];
    
    foreach ($required_fields as $field => $label) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[] = "$label is required.";
        }
    }
    
    // Validate email format
    if (!empty($_POST['email_address']) && !filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    
    // Validate contact number (basic validation)
    if (!empty($_POST['contactno']) && !preg_match('/^[0-9+\-\s()]+$/', $_POST['contactno'])) {
        $errors[] = "Please enter a valid contact number.";
    }
    
    // Validate relationship "Other" field
    if ($_POST['relationship'] === 'Other' && empty(trim($_POST['relationship_other'] ?? ''))) {
        $errors[] = "Please specify your relationship to the document owner.";
    }
    
    // Validate purpose "Other" field
    if ($_POST['purpose'] === 'Other' && empty(trim($_POST['purpose_other'] ?? ''))) {
        $errors[] = "Please specify the purpose of certification.";
    }
    
    // Validate authorization file if representative is checked
    if (isset($_POST['isRepresentative']) && (!isset($_FILES['authorization']) || $_FILES['authorization']['error'] !== UPLOAD_ERR_OK)) {
        $errors[] = "Authorization letter is required when acting as a representative.";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // Store customer data in session including certificate type
        $_SESSION['customer_data'] = [
            'certificate_type' => $certificate_type,
            'contactno' => trim($_POST['contactno']),
            'address' => trim($_POST['address']),
            'relationship' => trim($_POST['relationship'] === 'Other' ? $_POST['relationship_other'] : $_POST['relationship']),
            'purpose' => trim($_POST['purpose'] === 'Other' ? $_POST['purpose_other'] : $_POST['purpose']),
            'email_address' => trim($_POST['email_address']),
            'civilstatus' => trim($_POST['civilstatus']),
            'fullname' => trim($_POST['fullname']),
            'email' => trim($_POST['email_address']),
            'isRepresentative' => isset($_POST['isRepresentative']) ? true : false,
            'authorization' => null
        ];
        
        // Handle authorization file upload
        if (isset($_FILES['authorization']) && $_FILES['authorization']['error'] === UPLOAD_ERR_OK) {
            $authorization = $_FILES['authorization']['name'];
            $upload_dir = "../uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            if (move_uploaded_file($_FILES['authorization']['tmp_name'], "{$upload_dir}{$authorization}")) {
                $_SESSION['customer_data']['authorization'] = $authorization;
            } else {
                $errors[] = "Failed to upload authorization letter.";
            }
        }
        
        // If still no errors after file upload, redirect
        if (empty($errors)) {
            // Clean any output buffer before redirect
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Redirect to appropriate form based on certificate type
            switch($certificate_type) {
                case 'livebirth':
                    header("Location: forms/livebirth.php");
                    exit();
                    
                case 'death':
                    header("Location: forms/death.php");
                    exit();
                    
                case 'marriage':
                    header("Location: forms/marriage.php");
                    exit();
                    
                default:
                    header("Location: certificatetype.php");
                    exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Bootstrap & Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f9f9f9;
        }

        header {
            background-color: white;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 20px 30px;
            box-shadow: 0 8px 11px rgba(14, 55, 54, 0.15);
        }

        .logo-img {
            height: 45px;
            margin-right: 15px;
        }

        main {
            padding-top: 120px;
            padding-bottom: 40px;
        }

        .form-box {
            background-color: #f0f9f3;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .certificate-type-indicator {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .required::after {
            content: " *";
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error-alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include "includes/navbar.php"; ?>

<!-- Main Content -->
<main class="container">
    <div class="form-box">
        <!-- Back to Certificate Selection -->
        <a href="certificatetype.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Back to Certificate Selection
        </a>

        <!-- Certificate Type Indicator -->
        <div class="certificate-type-indicator">
            <i class="fas fa-certificate me-2"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </div>

        <h4 class="mt-3 mb-3 fw-bold">Requester's Information</h4>
        
        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="error-alert">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="customerForm" novalidate>
            <!-- Hidden field to preserve certificate type -->
            <input type="hidden" name="certificate_type" value="<?php echo htmlspecialchars($certificate_type); ?>" id="certificateTypeHidden">
            
            <div class="row g-3" id="requesterInfoFields">
                <div class="col-md-4">
                    <label class="form-label required">Full Name</label>
                    <input type="text" 
                           class="form-control <?php echo in_array('Full Name is required.', $errors) ? 'is-invalid' : ''; ?>" 
                           name="fullname" 
                           value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>"
                           required />
                    <?php if (in_array('Full Name is required.', $errors)): ?>
                        <div class="invalid-feedback">Full Name is required.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Email Address</label>
                    <input type="email" 
                           class="form-control <?php echo (in_array('Email Address is required.', $errors) || in_array('Please enter a valid email address.', $errors)) ? 'is-invalid' : ''; ?>" 
                           name="email_address" 
                           value="<?php echo htmlspecialchars($_POST['email_address'] ?? ''); ?>"
                           required />
                    <?php if (in_array('Email Address is required.', $errors)): ?>
                        <div class="invalid-feedback">Email Address is required.</div>
                    <?php elseif (in_array('Please enter a valid email address.', $errors)): ?>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Contact Number</label>
                    <input type="text" 
                           class="form-control <?php echo (in_array('Contact Number is required.', $errors) || in_array('Please enter a valid contact number.', $errors)) ? 'is-invalid' : ''; ?>" 
                           name="contactno" 
                           value="<?php echo htmlspecialchars($_POST['contactno'] ?? ''); ?>"
                           required />
                    <?php if (in_array('Contact Number is required.', $errors)): ?>
                        <div class="invalid-feedback">Contact Number is required.</div>
                    <?php elseif (in_array('Please enter a valid contact number.', $errors)): ?>
                        <div class="invalid-feedback">Please enter a valid contact number.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Relationship to the Document Owner</label>
                    <select class="form-select <?php echo (in_array('Relationship to Document Owner is required.', $errors) || in_array('Please specify your relationship to the document owner.', $errors)) ? 'is-invalid' : ''; ?>" 
                            name="relationship" 
                            id="relationshipSelect" 
                            required 
                            onchange="toggleOtherRelationship(this)">
                        <option value="">Select</option>
                        <option value="SELF" <?php echo ($_POST['relationship'] ?? '') === 'SELF' ? 'selected' : ''; ?>>SELF</option>
                        <option value="Parent" <?php echo ($_POST['relationship'] ?? '') === 'Parent' ? 'selected' : ''; ?>>Parent</option>
                        <option value="Guardian" <?php echo ($_POST['relationship'] ?? '') === 'Guardian' ? 'selected' : ''; ?>>Guardian</option>
                        <option value="Relative" <?php echo ($_POST['relationship'] ?? '') === 'Relative' ? 'selected' : ''; ?>>Relative</option>
                        <option value="Other" <?php echo ($_POST['relationship'] ?? '') === 'Other' ? 'selected' : ''; ?>>Others (please specify)</option>
                    </select>
                    <input type="text" 
                           class="form-control mt-2" 
                           name="relationship_other" 
                           id="relationshipOtherInput" 
                           placeholder="Please specify relationship" 
                           value="<?php echo htmlspecialchars($_POST['relationship_other'] ?? ''); ?>"
                           style="display:<?php echo ($_POST['relationship'] ?? '') === 'Other' ? 'block' : 'none'; ?>;" />
                    <?php if (in_array('Relationship to Document Owner is required.', $errors)): ?>
                        <div class="invalid-feedback">Relationship to Document Owner is required.</div>
                    <?php elseif (in_array('Please specify your relationship to the document owner.', $errors)): ?>
                        <div class="invalid-feedback">Please specify your relationship to the document owner.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Civil Status</label>
                    <select class="form-select <?php echo in_array('Civil Status is required.', $errors) ? 'is-invalid' : ''; ?>" 
                            name="civilstatus" 
                            required>
                        <option value="" disabled <?php echo empty($_POST['civilstatus']) ? 'selected' : ''; ?>>Select Civil Status</option>
                        <option value="Single" <?php echo ($_POST['civilstatus'] ?? '') === 'Single' ? 'selected' : ''; ?>>Single</option>
                        <option value="Married" <?php echo ($_POST['civilstatus'] ?? '') === 'Married' ? 'selected' : ''; ?>>Married</option>
                        <option value="Widowed" <?php echo ($_POST['civilstatus'] ?? '') === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                        <option value="Divorced" <?php echo ($_POST['civilstatus'] ?? '') === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                    </select>
                    <?php if (in_array('Civil Status is required.', $errors)): ?>
                        <div class="invalid-feedback">Civil Status is required.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Purpose of the Certification</label>
                    <select class="form-select <?php echo (in_array('Purpose of Certification is required.', $errors) || in_array('Please specify the purpose of certification.', $errors)) ? 'is-invalid' : ''; ?>" 
                            name="purpose" 
                            id="purposeSelect" 
                            required 
                            onchange="toggleOtherPurpose(this)">
                        <option value="" disabled <?php echo empty($_POST['purpose']) ? 'selected' : ''; ?>>Purpose</option>
                        <option value="Enrollment" <?php echo ($_POST['purpose'] ?? '') === 'Enrollment' ? 'selected' : ''; ?>>Enrollment</option>
                        <option value="Legal" <?php echo ($_POST['purpose'] ?? '') === 'Legal' ? 'selected' : ''; ?>>Legal</option>
                        <option value="Travel" <?php echo ($_POST['purpose'] ?? '') === 'Travel' ? 'selected' : ''; ?>>Travel</option>
                        <option value="Employment" <?php echo ($_POST['purpose'] ?? '') === 'Employment' ? 'selected' : ''; ?>>Employment</option>
                        <option value="Passport" <?php echo ($_POST['purpose'] ?? '') === 'Passport' ? 'selected' : ''; ?>>Passport</option>
                        <option value="Claim Benefits" <?php echo ($_POST['purpose'] ?? '') === 'Claim Benefits' ? 'selected' : ''; ?>>Claim Benefits</option>
                        <option value="Government Requirement" <?php echo ($_POST['purpose'] ?? '') === 'Government Requirement' ? 'selected' : ''; ?>>Government Requirement</option>
                        <option value="Bank Requirement" <?php echo ($_POST['purpose'] ?? '') === 'Bank Requirement' ? 'selected' : ''; ?>>Bank Requirement</option>
                        <option value="School Requirement" <?php echo ($_POST['purpose'] ?? '') === 'School Requirement' ? 'selected' : ''; ?>>School Requirement</option>
                        <option value="Marriage" <?php echo ($_POST['purpose'] ?? '') === 'Marriage' ? 'selected' : ''; ?>>Marriage</option>
                        <option value="Other" <?php echo ($_POST['purpose'] ?? '') === 'Other' ? 'selected' : ''; ?>>Others (please specify)</option>
                    </select>
                    <input type="text" 
                           class="form-control mt-2" 
                           name="purpose_other" 
                           id="purposeOtherInput" 
                           placeholder="Please specify purpose" 
                           value="<?php echo htmlspecialchars($_POST['purpose_other'] ?? ''); ?>"
                           style="display:<?php echo ($_POST['purpose'] ?? '') === 'Other' ? 'block' : 'none'; ?>;" />
                    <?php if (in_array('Purpose of Certification is required.', $errors)): ?>
                        <div class="invalid-feedback">Purpose of Certification is required.</div>
                    <?php elseif (in_array('Please specify the purpose of certification.', $errors)): ?>
                        <div class="invalid-feedback">Please specify the purpose of certification.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <label class="form-label required">Home Address</label>
                    <input type="text" 
                           class="form-control <?php echo in_array('Home Address is required.', $errors) ? 'is-invalid' : ''; ?>" 
                           name="address" 
                           value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                           required />
                    <?php if (in_array('Home Address is required.', $errors)): ?>
                        <div class="invalid-feedback">Home Address is required.</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="d-flex align-items-center" style="gap: 18px;">
                        <label class="form-label mb-0" for="isRepresentativeCheckbox" style="font-weight: 800;">
                            Are you the document owner or authorized representative?
                        </label>
                        <input class="form-check-input ms-2" 
                               type="checkbox" 
                               id="isRepresentativeCheckbox" 
                               name="isRepresentative"
                               <?php echo isset($_POST['isRepresentative']) ? 'checked' : ''; ?>
                               style="width: 1.5em; height: 1.5em; border: 2px solid #0ea5e9; accent-color: #0ea5e9; box-shadow: 0 0 0 2px #38bdf8;" />
                        <label class="form-check-label mb-0" for="isRepresentativeCheckbox" style="font-weight: 500;">
                            If you're a representative, kindly check the box.
                        </label>
                    </div>
                </div>
                <div class="col-md-12" id="authLetterField" style="display:<?php echo isset($_POST['isRepresentative']) ? 'block' : 'none'; ?>;">
                    <label class="form-label required">Upload Authorization Letter</label>
                    <input type="file" 
                           class="form-control <?php echo in_array('Authorization letter is required when acting as a representative.', $errors) ? 'is-invalid' : ''; ?>" 
                           name="authorization" 
                           id="authLetterInput"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                    <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX</small>
                    <?php if (in_array('Authorization letter is required when acting as a representative.', $errors)): ?>
                        <div class="invalid-feedback">Authorization letter is required when acting as a representative.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary px-4 py-2" id="submitBtn">
                    Next <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
            <br /><hr />
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const repCheckbox = document.getElementById('isRepresentativeCheckbox');
        const authLetterField = document.getElementById('authLetterField');
        const authLetterInput = document.getElementById('authLetterInput');
        const form = document.getElementById('customerForm');
        const submitBtn = document.getElementById('submitBtn');

        // Show/hide authorization letter field
        if (repCheckbox) {
            repCheckbox.addEventListener('change', function() {
                if (repCheckbox.checked) {
                    authLetterField.style.display = 'block';
                    authLetterInput.required = true;
                } else {
                    authLetterField.style.display = 'none';
                    authLetterInput.required = false;
                    authLetterInput.value = '';
                    authLetterInput.classList.remove('is-invalid');
                }
            });
        }

        // Enhanced form validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const certificateType = document.getElementById('certificateTypeHidden').value;
            
            console.log('🚀 Form submission started');
            console.log('Certificate Type:', certificateType);
            
            // Remove existing error classes
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            
            // Validate required fields
            const requiredFields = [
                { field: 'fullname', label: 'Full Name' },
                { field: 'email_address', label: 'Email Address' },
                { field: 'contactno', label: 'Contact Number' },
                { field: 'relationship', label: 'Relationship' },
                { field: 'civilstatus', label: 'Civil Status' },
                { field: 'purpose', label: 'Purpose' },
                { field: 'address', label: 'Home Address' }
            ];
            
            requiredFields.forEach(({ field, label }) => {
                const element = document.querySelector(`[name="${field}"]`);
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = `${label} is required.`;
                    element.parentNode.appendChild(feedback);
                    isValid = false;
                }
            });
            
            // Email validation
            const email = document.querySelector('[name="email_address"]');
            if (email.value && !email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                email.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Please enter a valid email address.';
                email.parentNode.appendChild(feedback);
                isValid = false;
            }
            
            // Authorization file validation
            if (repCheckbox.checked && !authLetterInput.files[0]) {
                authLetterInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Authorization letter is required.';
                authLetterInput.parentNode.appendChild(feedback);
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                console.log('❌ Form validation failed');
                
                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return false;
            }
            
            console.log('✅ Form validation passed');
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
            submitBtn.disabled = true;
        });

        // Log current page state
        console.log('📄 Page loaded with certificate type:', '<?php echo $certificate_type; ?>');
        console.log('🔗 Current URL:', window.location.href);
    });

    function toggleOtherRelationship(select) {
        const otherInput = document.getElementById('relationshipOtherInput');
        if (select.value === 'Other') {
            otherInput.style.display = 'block';
            otherInput.required = true;
        } else {
            otherInput.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }

    function toggleOtherPurpose(select) {
        const otherInput = document.getElementById('purposeOtherInput');
        if (select.value === 'Other') {
            otherInput.style.display = 'block';
            otherInput.required = true;
        } else {
            otherInput.style.display = 'none';
            otherInput.required = false;
            otherInput.value = '';
        }
    }
</script>

</body>
</html>