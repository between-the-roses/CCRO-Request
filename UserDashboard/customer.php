<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . "/../backend/db.php";
include "includes/navbar.php";

if (!$conn) {
  echo "<div class='alert alert-danger mt-3'>Database connection failed.</div>";
  exit;
}

// Get certificate type from session or URL parameter
$certificate_type = $_SESSION['certificate_type'] ?? $_GET['type'] ?? 'marriage';

// Validate certificate type
if (!in_array($certificate_type, ['marriage', 'livebirth', 'death'])) {
    $certificate_type = 'marriage';
}

// Store in session for consistency
$_SESSION['certificate_type'] = $certificate_type;

// Set page title based on certificate type
$page_titles = [
    'marriage' => 'Marriage Certificate Request',
    'livebirth' => 'Birth Certificate Request', 
    'death' => 'Death Certificate Request'
];
$page_title = $page_titles[$certificate_type];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store customer data in session instead of database
    $_SESSION['customer_data'] = [
        'contactno' => trim($_POST['contactno']),
        'address' => trim($_POST['address']),
        'relationship' => trim($_POST['relationship'] === 'Other' ? $_POST['relationship_other'] : $_POST['relationship']),
        'purpose' => trim($_POST['purpose'] === 'Other' ? $_POST['purpose_other'] : $_POST['purpose']),
        'email_address' => trim($_POST['email_address']),
        'civilstatus' => isset($_POST['civilstatus']) ? trim($_POST['civilstatus']) : '',
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
        move_uploaded_file($_FILES['authorization']['tmp_name'], $upload_dir . $authorization);
        $_SESSION['customer_data']['authorization'] = $authorization;
    }
    
    // Redirect to certificate form based on the certificate type in session
    switch($certificate_type) {
        case 'marriage':
            header("Location: forms/marriage.php");
            break;
        case 'livebirth':
            header("Location: forms/livebirth.php");
            break;
        case 'death':
            header("Location: forms/death.php");
            break;
        default:
            header("Location: certificatetype.php");
    }
    exit;
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
    </style>
</head>
<body>

<main class="container">
    <div class="form-box">
        <!-- Certificate Type Indicator -->
        <div class="certificate-type-indicator">
            <i class="fas fa-certificate me-2"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </div>

        <h4 class="mt-3 mb-3 fw-bold">Requester's Information</h4>
        
        <!-- Debug info (remove in production) -->
        <div class="alert alert-info mb-3">
            <small>
                <strong>Debug:</strong> Certificate Type = <?php echo htmlspecialchars($certificate_type); ?>
                <?php if (isset($_SESSION['certificate_type'])): ?>
                    | Session Type = <?php echo htmlspecialchars($_SESSION['certificate_type']); ?>
                <?php endif; ?>
            </small>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <!-- Hidden field to preserve certificate type -->
            <input type="hidden" name="certificate_type" value="<?php echo htmlspecialchars($certificate_type); ?>">
            
            <div class="row g-3" id="requesterInfoFields">
                <div class="col-md-4">
                    <label class="form-label required">Full Name</label>
                    <input type="text" class="form-control" name="fullname" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Email Address</label>
                    <input type="email" class="form-control" name="email_address" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Contact Number</label>
                    <input type="text" class="form-control" name="contactno" required />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Relationship to the Document Owner</label>
                    <select class="form-select" name="relationship" id="relationshipSelect" required onchange="toggleOtherRelationship(this)">
                        <option selected>Select</option>
                        <option value="SELF">SELF</option>
                        <option value="Parent">Parent</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Relative">Relative</option>
                        <option value="Other">Others (please specify)</option>
                    </select>
                    <input type="text" class="form-control mt-2" name="relationship_other" id="relationshipOtherInput" placeholder="Please specify relationship" style="display:none;" />
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Civil Status</label>
                    <select class="form-select" name="civilstatus" required>
                        <option value="" disabled selected>Select Civil Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Divorced">Divorced</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label required">Purpose of the Certification</label>
                    <select class="form-select" name="purpose" id="purposeSelect" required onchange="toggleOtherPurpose(this)">
                        <option disabled selected>Purpose</option>
                        <option>Enrollment</option>
                        <option>Legal</option>
                        <option>Travel</option>
                        <option>Employment</option>
                        <option>Passport</option>
                        <option>Claim Benefits</option>
                        <option>Government Requirement</option>
                        <option>Bank Requirement</option>
                        <option>School Requirement</option>
                        <option>Marriage</option>
                        <option value="Other">Others (please specify)</option>
                    </select>
                    <input type="text" class="form-control mt-2" name="purpose_other" id="purposeOtherInput" placeholder="Please specify purpose" style="display:none;" />
                </div>
                <div class="col-md-12">
                    <label class="form-label required">Home Address</label>
                    <input type="text" class="form-control" name="address" required />
                </div>
                <div class="col-md-12">
                    <div class="d-flex align-items-center" style="gap: 18px;">
                        <label class="form-label mb-0" for="isRepresentativeCheckbox" style="font-weight: 800;">
                            Are you the document owner or authorized representative?
                        </label>
                        <input class="form-check-input ms-2" type="checkbox" id="isRepresentativeCheckbox" name="isRepresentative"
                        style="width: 1.5em; height: 1.5em; border: 2px solid #0ea5e9; accent-color: #0ea5e9; box-shadow: 0 0 0 2px #38bdf8;" />
                        <label class="form-check-label mb-0" for="isRepresentativeCheckbox" style="font-weight: 500;">
                            If you're a representative, kindly check the box.
                        </label>
                    </div>
                </div>
                <div class="col-md-12" id="authLetterField" style="display:none;">
                    <label class="form-label required">Upload Authorization Letter</label>
                    <input type="file" class="form-control" name="authorization" id="authLetterInput" />
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-primary px-4 py-2">Next</button>
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

        // Show/hide authorization letter field
        if (repCheckbox) {
            repCheckbox.addEventListener('change', function() {
                if (repCheckbox.checked) {
                    authLetterField.style.display = '';
                    authLetterInput.required = false;
                } else {
                    authLetterField.style.display = 'none';
                    authLetterInput.required = false;
                    authLetterInput.value = '';
                    authLetterInput.classList.remove('is-invalid');
                }
            });
        }
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