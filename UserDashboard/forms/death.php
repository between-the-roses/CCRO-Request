<?php
session_start();
include __DIR__ . "/../../backend/db.php";
include "../includes/navbar.php";

// Check if customer data exists in session
if (!isset($_SESSION['customer_data'])) {
    header("Location: http://localhost/CCRO-Request/UserDashboard/customer.php");
    exit;
}

$created_date = $_SESSION['customer_created_date'] ?? date('m/d/Y');
$_SESSION['customer_created_date'] = date('m/d/Y');

// Form validation and processing - ONLY process if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields first
    $errors = [];
    
    // Check if this is array data (multiple certificates) or single
    $is_multiple = is_array($_POST['deceased_firstname'] ?? null);
    
    if ($is_multiple) {
        $count = count($_POST['deceased_firstname']);
        for ($i = 0; $i < $count; $i++) {
            if (empty(trim($_POST['deceased_firstname'][$i] ?? ''))) {
                $errors[] = "First name is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['deceased_lastname'][$i] ?? ''))) {
                $errors[] = "Last name is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['deathdate'][$i] ?? '')) {
                $errors[] = "Death date is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['birthdate'][$i] ?? '')) {
                $errors[] = "Birth date is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['sex'][$i] ?? '')) {
                $errors[] = "Sex is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['deathplace'][$i] ?? ''))) {
                $errors[] = "Place of death is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['civilstatus_deceased'][$i] ?? '')) {
                $errors[] = "Civil status is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['religion'][$i] ?? ''))) {
                $errors[] = "Religion is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['citizenship'][$i] ?? ''))) {
                $errors[] = "Citizenship is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['residence'][$i] ?? ''))) {
                $errors[] = "Residence is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['occupation'][$i] ?? ''))) {
                $errors[] = "Occupation is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['fathersname'][$i] ?? ''))) {
                $errors[] = "Father's name is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['mothersname'][$i] ?? ''))) {
                $errors[] = "Mother's name is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['corpsedisposal'][$i] ?? '')) {
                $errors[] = "Corpse disposal method is required for certificate #" . ($i + 1);
            }
            if (empty(trim($_POST['cemeteryaddress'][$i] ?? ''))) {
                $errors[] = "Cemetery address is required for certificate #" . ($i + 1);
            }
            if (empty($_POST['copies'][$i] ?? '')) {
                $errors[] = "Number of copies is required for certificate #" . ($i + 1);
            }
        }
    } else {
        // Single certificate validation
        if (empty(trim($_POST['deceased_firstname'] ?? ''))) {
            $errors[] = "First name is required";
        }
        if (empty(trim($_POST['deceased_lastname'] ?? ''))) {
            $errors[] = "Last name is required";
        }
        if (empty($_POST['deathdate'] ?? '')) {
            $errors[] = "Death date is required";
        }
        if (empty($_POST['birthdate'] ?? '')) {
            $errors[] = "Birth date is required";
        }
        if (empty($_POST['sex'] ?? '')) {
            $errors[] = "Sex is required";
        }
        if (empty(trim($_POST['deathplace'] ?? ''))) {
            $errors[] = "Place of death is required";
        }
        if (empty($_POST['civilstatus_deceased'] ?? '')) {
            $errors[] = "Civil status is required";
        }
        if (empty(trim($_POST['religion'] ?? ''))) {
            $errors[] = "Religion is required";
        }
        if (empty(trim($_POST['citizenship'] ?? ''))) {
            $errors[] = "Citizenship is required";
        }
        if (empty(trim($_POST['residence'] ?? ''))) {
            $errors[] = "Residence is required";
        }
        if (empty(trim($_POST['occupation'] ?? ''))) {
            $errors[] = "Occupation is required";
        }
        if (empty(trim($_POST['fathersname'] ?? ''))) {
            $errors[] = "Father's name is required";
        }
        if (empty(trim($_POST['mothersname'] ?? ''))) {
            $errors[] = "Mother's name is required";
        }
        if (empty($_POST['corpsedisposal'] ?? '')) {
            $errors[] = "Corpse disposal method is required";
        }
        if (empty(trim($_POST['cemeteryaddress'] ?? ''))) {
            $errors[] = "Cemetery address is required";
        }
        if (empty($_POST['copies'] ?? '')) {
            $errors[] = "Number of copies is required";
        }
    }
    
    // If there are validation errors, don't process the form
    if (!empty($errors)) {
        // We'll display these errors in the HTML section
        error_log("Death certificate form validation failed: " . implode(', ', $errors));
    } else {
        // Only process if validation passes
        $customer_data = $_SESSION['customer_data'];
        $certificate_type = 'death';

        try {
            // Get copies from form data (first certificate as fallback)
            $copies = 1;
            if (isset($_POST['copies'])) {
                if (is_array($_POST['copies'])) {
                    $copies = intval($_POST['copies'][0]);
                } else {
                    $copies = intval($_POST['copies']);
                }
            }

            // Insert customer first
            $stmt = $conn->prepare("INSERT INTO customer (fullname, contactno, address, relationship, civilstatus, purpose, copies, certificate_type, email_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?::cert_type, ?)");
            $stmt->execute([
                $customer_data['fullname'],
                $customer_data['contactno'],
                $customer_data['address'],
                $customer_data['relationship'],
                $customer_data['civilstatus'],
                $customer_data['purpose'],
                $copies,
                $certificate_type,
                $customer_data['email_address']
            ]);
            $customer_id = $conn->lastInsertId();

            // Generate transaction number using customer_id
            include "../transaction.php";
            $transaction_number = generateTransactionFromId($customer_id);
            $_SESSION['transaction_number'] = $transaction_number;

            // Handle multiple death certificates
            if ($is_multiple) {
                $count = count($_POST['deceased_firstname']);
                for ($i = 0; $i < $count; $i++) {
                    $registry_id = trim($_POST['registry_id'][$i] ?? '');
                    $registry_id = ($registry_id === '') ? null : intval($registry_id);

                    $deceasedname = trim(($_POST['deceased_firstname'][$i] ?? '') . ' ' . ($_POST['deceased_middlename'][$i] ?? '') . ' ' . ($_POST['deceased_lastname'][$i] ?? ''));
                    $deathdate = $_POST['deathdate'][$i] ?? '';
                    $birthdate = $_POST['birthdate'][$i] ?? '';
                    $age = intval($_POST['age'][$i] ?? 0);
                    $sex = $_POST['sex'][$i] ?? '';
                    $deathplace = trim($_POST['deathplace'][$i] ?? '');
                    $civilstatus = $_POST['civilstatus_deceased'][$i] ?? '';
                    $religion = trim($_POST['religion'][$i] ?? '');
                    $citizenship = trim($_POST['citizenship'][$i] ?? '');
                    $residence = trim($_POST['residence'][$i] ?? '');
                    $occupation = trim($_POST['occupation'][$i] ?? '');
                    $fathersname = trim($_POST['fathersname'][$i] ?? '');
                    $mothersname = trim($_POST['mothersname'][$i] ?? '');
                    $corpsedisposal = $_POST['corpsedisposal'][$i] ?? '';
                    $cemeteryaddress = trim($_POST['cemeteryaddress'][$i] ?? '');

                    $stmt = $conn->prepare("INSERT INTO death (customer_id, registry_id, deceasedname, deathdate, birthdate, age, sex, deathplace, civilstatus, religion, citizenship, residence, occupation, fathersname, mothersname, corpsedisposal, cemeteryaddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$customer_id, $registry_id, $deceasedname, $deathdate, $birthdate, $age, $sex, $deathplace, $civilstatus, $religion, $citizenship, $residence, $occupation, $fathersname, $mothersname, $corpsedisposal, $cemeteryaddress]);
                }
            } else {
                // Single certificate fallback
                $registry_id = trim($_POST['registry_id'] ?? '');
                $registry_id = ($registry_id === '') ? null : intval($registry_id);

                $deceasedname = trim(($_POST['deceased_firstname'] ?? '') . ' ' . ($_POST['deceased_middlename'] ?? '') . ' ' . ($_POST['deceased_lastname'] ?? ''));
                $deathdate = $_POST['deathdate'] ?? '';
                $birthdate = $_POST['birthdate'] ?? '';
                $age = intval($_POST['age'] ?? 0);
                $sex = $_POST['sex'] ?? '';
                $deathplace = trim($_POST['deathplace'] ?? '');
                $civilstatus = $_POST['civilstatus_deceased'] ?? '';
                $religion = trim($_POST['religion'] ?? '');
                $citizenship = trim($_POST['citizenship'] ?? '');
                $residence = trim($_POST['residence'] ?? '');
                $occupation = trim($_POST['occupation'] ?? '');
                $fathersname = trim($_POST['fathersname'] ?? '');
                $mothersname = trim($_POST['mothersname'] ?? '');
                $corpsedisposal = $_POST['corpsedisposal'] ?? '';
                $cemeteryaddress = trim($_POST['cemeteryaddress'] ?? '');

                $stmt = $conn->prepare("INSERT INTO death (customer_id, registry_id, deceasedname, deathdate, birthdate, age, sex, deathplace, civilstatus, religion, citizenship, residence, occupation, fathersname, mothersname, corpsedisposal, cemeteryaddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$customer_id, $registry_id, $deceasedname, $deathdate, $birthdate, $age, $sex, $deathplace, $civilstatus, $religion, $citizenship, $residence, $occupation, $fathersname, $mothersname, $corpsedisposal, $cemeteryaddress]);
            }

            // Clear customer data but keep transaction info
            unset($_SESSION['customer_data']);
            $_SESSION['customer_id'] = $customer_id;

            // Show success message and redirect
            echo "<div style='position:fixed;top:30px;right:30px;z-index:2000;min-width:300px;' class='alert alert-success shadow'>Death certificate request submitted successfully!</div>";
            echo "<script>setTimeout(function(){ window.location.href = '../verification.php'; }, 2000);</script>";
            exit;

        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
            error_log("Death certificate database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Death Certificate Form</title>

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

    h1 {
      font-size: 2rem;
      font-weight: 700;
    }

    h3 {
      font-weight: 600;
      margin-bottom: 20px;
    }

    .btn-next {
      background-color: #38bdf8;
      color: white;
    }

    .btn-add {
      background-color: #d2f8d2;
    }

    .required::after {
      content: " *";
      color: red;
      font-weight: bold;
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
    
    /* Styles for checklist modal */
    .checklist-modal .modal-content {
      border-radius: 20px;
      background-color: #f0f9ff;
    }
    .checklist-modal .modal-header {
      background-color: #38bdf8;
      color: white;
      border-radius: 20px 20px 0 0;
    }
    .checklist-modal .modal-body {
      padding: 30px;
    }
    .checklist-modal .proceed-btn {
      background-color: #38bdf8;
      color: white;
      padding: 10px 30px;
      border-radius: 50px;
      border: none;
      font-weight: 600;
    }
    .checklist-item {
      margin-bottom: 10px;
    }
    .checklist-header {
      background-color: #e6f7ff;
      padding: 10px 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      text-align: center;
      font-weight: 500;
    }
  </style>
</head>
<body>

  <main class="container">
    <div class="form-box">
      <h1 class="mb-4 text-center text-lg-start">Death Certificate Details</h1>
      
      <?php if (isset($_SESSION['transaction_number'])): ?>
      <div class="alert alert-info mb-3 text-center">
          <h5 class="mb-1">
              <i class="fas fa-receipt me-2"></i>
              Transaction Number: <strong><?php echo htmlspecialchars($_SESSION['transaction_number']); ?></strong>
          </h5>
          <small>Please save this transaction number for your records</small>
      </div>
      <?php endif; ?>

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
      
      <p class="text-center text-lg-start mb-4">
        Please fill out the form below to request a death certificate. Ensure all required fields are completed.
        If you need to add another certificate, click the "Add Another Certificate" button.
      </p>

      <?php
      // Show customer summary (from session) before the death form
      if (isset($_SESSION['customer_data'])) {
        $cd = $_SESSION['customer_data'];
        ?>
        <div class="mb-4 p-3 rounded bg-white border shadow">
          <h3 class="mb-3">Requester's Information</h3>
          <dl class="row mb-0">
            <dt class="col-sm-4">Full Name</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['fullname'] ?? ''); ?></dd>

            <dt class="col-sm-4">Contact No.</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['contactno'] ?? ''); ?></dd>

            <dt class="col-sm-4">Address</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['address'] ?? ''); ?></dd>

            <dt class="col-sm-4">Relationship</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['relationship'] ?? ''); ?></dd>
          </dl>
        </div>
        <?php
      }
      ?>
      
      <form class="death-form" id="deathCertForm" method="POST" novalidate>
        <h3>Certificate #1</h3>

        <!-- Certificate Info -->
        <div class="mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Registry Number (Optional)</label>
              <input type="text" class="form-control" name="registry_id[]" value="<?php echo htmlspecialchars(isset($_POST['registry_id']) && is_array($_POST['registry_id']) ? ($_POST['registry_id'][0] ?? '') : ($_POST['registry_id'] ?? '')); ?>" />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Number of Copies</label>
              <select class="form-select <?php echo in_array('Number of copies is required for certificate #1', $errors ?? []) ? 'is-invalid' : ''; ?>" name="copies[]" required>
                <option value="" disabled <?php echo empty(isset($_POST['copies']) && is_array($_POST['copies']) ? ($_POST['copies'][0] ?? '') : ($_POST['copies'] ?? '')) ? 'selected' : ''; ?>>Choose...</option>
                <option value="1" <?php echo (isset($_POST['copies']) && is_array($_POST['copies']) ? ($_POST['copies'][0] ?? '') : ($_POST['copies'] ?? '')) === '1' ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo (isset($_POST['copies']) && is_array($_POST['copies']) ? ($_POST['copies'][0] ?? '') : ($_POST['copies'] ?? '')) === '2' ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo (isset($_POST['copies']) && is_array($_POST['copies']) ? ($_POST['copies'][0] ?? '') : ($_POST['copies'] ?? '')) === '3' ? 'selected' : ''; ?>>3</option>
              </select>
              <?php if (in_array('Number of copies is required for certificate #1', $errors ?? [])): ?>
                  <div class="invalid-feedback">Number of copies is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Deceased Person Complete Name -->
        <div class="mb-4">
          <h3>Complete Name of the Deceased</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control <?php echo in_array('First name is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="deceased_firstname[]" value="<?php echo htmlspecialchars(isset($_POST['deceased_firstname']) && is_array($_POST['deceased_firstname']) ? ($_POST['deceased_firstname'][0] ?? '') : ($_POST['deceased_firstname'] ?? '')); ?>" required />
              <?php if (in_array('First name is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">First name is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="deceased_middlename[]" value="<?php echo htmlspecialchars(isset($_POST['deceased_middlename']) && is_array($_POST['deceased_middlename']) ? ($_POST['deceased_middlename'][0] ?? '') : ($_POST['deceased_middlename'] ?? '')); ?>" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control <?php echo in_array('Last name is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="deceased_lastname[]" value="<?php echo htmlspecialchars(isset($_POST['deceased_lastname']) && is_array($_POST['deceased_lastname']) ? ($_POST['deceased_lastname'][0] ?? '') : ($_POST['deceased_lastname'] ?? '')); ?>" required />
              <?php if (in_array('Last name is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Last name is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Death and Birth Dates -->
        <div class="mb-4">
          <h3>Date Information</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">Date of Death</label>
              <input type="date" class="form-control <?php echo in_array('Death date is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="deathdate[]" value="<?php echo htmlspecialchars(isset($_POST['deathdate']) && is_array($_POST['deathdate']) ? ($_POST['deathdate'][0] ?? '') : ($_POST['deathdate'] ?? '')); ?>" required />
              <?php if (in_array('Death date is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Death date is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-4">
              <label class="form-label required">Date of Birth</label>
              <input type="date" class="form-control <?php echo in_array('Birth date is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="birthdate[]" value="<?php echo htmlspecialchars(isset($_POST['birthdate']) && is_array($_POST['birthdate']) ? ($_POST['birthdate'][0] ?? '') : ($_POST['birthdate'] ?? '')); ?>" required />
              <?php if (in_array('Birth date is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Birth date is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-4">
              <label class="form-label required">Age at Death</label>
              <input type="number" class="form-control" name="age[]" value="<?php echo htmlspecialchars(isset($_POST['age']) && is_array($_POST['age']) ? ($_POST['age'][0] ?? '') : ($_POST['age'] ?? '')); ?>" min="0" max="150" required readonly style="background-color: #f8f9fa;" />
              <small class="text-muted">Age will be calculated automatically from birth and death dates</small>
            </div>
            <div class="col-md-12">
              <label class="form-label required">Place of Death (City/Municipality/Province)</label>
              <input type="text" class="form-control <?php echo in_array('Place of death is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="deathplace[]" value="<?php echo htmlspecialchars(isset($_POST['deathplace']) && is_array($_POST['deathplace']) ? ($_POST['deathplace'][0] ?? '') : ($_POST['deathplace'] ?? '')); ?>" required />
              <?php if (in_array('Place of death is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Place of death is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Age and Sex -->
        <div class="mb-4">
          <h3>Deceased Personal Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Sex</label>
              <select class="form-select <?php echo in_array('Sex is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="sex[]" required>
                <option value="" disabled <?php echo empty(isset($_POST['sex']) && is_array($_POST['sex']) ? ($_POST['sex'][0] ?? '') : ($_POST['sex'] ?? '')) ? 'selected' : ''; ?>>Choose...</option>
                <option value="Male" <?php echo (isset($_POST['sex']) && is_array($_POST['sex']) ? ($_POST['sex'][0] ?? '') : ($_POST['sex'] ?? '')) === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($_POST['sex']) && is_array($_POST['sex']) ? ($_POST['sex'][0] ?? '') : ($_POST['sex'] ?? '')) === 'Female' ? 'selected' : ''; ?>>Female</option>
              </select>
              <?php if (in_array('Sex is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Sex is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Civil Status</label>
              <select class="form-select <?php echo in_array('Civil status is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="civilstatus_deceased[]" required>
                <option value="" disabled <?php echo empty(isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) ? 'selected' : ''; ?>>Choose...</option>
                <option value="Single" <?php echo (isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) === 'Single' ? 'selected' : ''; ?>>Single</option>
                <option value="Married" <?php echo (isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) === 'Married' ? 'selected' : ''; ?>>Married</option>
                <option value="Widowed" <?php echo (isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                <option value="Divorced" <?php echo (isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                <option value="Separated" <?php echo (isset($_POST['civilstatus_deceased']) && is_array($_POST['civilstatus_deceased']) ? ($_POST['civilstatus_deceased'][0] ?? '') : ($_POST['civilstatus_deceased'] ?? '')) === 'Separated' ? 'selected' : ''; ?>>Separated</option>
              </select>
              <?php if (in_array('Civil status is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Civil status is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Religion</label>
              <input type="text" class="form-control <?php echo in_array('Religion is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="religion[]" value="<?php echo htmlspecialchars(isset($_POST['religion']) && is_array($_POST['religion']) ? ($_POST['religion'][0] ?? '') : ($_POST['religion'] ?? '')); ?>" required />
              <?php if (in_array('Religion is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Religion is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Citizenship</label>
              <input type="text" class="form-control <?php echo in_array('Citizenship is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="citizenship[]" value="<?php echo htmlspecialchars(isset($_POST['citizenship']) && is_array($_POST['citizenship']) ? ($_POST['citizenship'][0] ?? '') : ($_POST['citizenship'] ?? '')); ?>" required />
              <?php if (in_array('Citizenship is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Citizenship is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Occupation</label>
              <input type="text" class="form-control <?php echo in_array('Occupation is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="occupation[]" value="<?php echo htmlspecialchars(isset($_POST['occupation']) && is_array($_POST['occupation']) ? ($_POST['occupation'][0] ?? '') : ($_POST['occupation'] ?? '')); ?>" required />
              <?php if (in_array('Occupation is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Occupation is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Residence</label>
              <input type="text" class="form-control <?php echo in_array('Residence is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="residence[]" value="<?php echo htmlspecialchars(isset($_POST['residence']) && is_array($_POST['residence']) ? ($_POST['residence'][0] ?? '') : ($_POST['residence'] ?? '')); ?>" required />
              <?php if (in_array('Residence is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Residence is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Parents Information -->
        <div class="mb-4">
          <h3>Parents Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Name of Father (Full Name)</label>
              <input type="text" class="form-control <?php echo in_array("Father's name is required", $errors ?? []) ? 'is-invalid' : ''; ?>" name="fathersname[]" value="<?php echo htmlspecialchars(isset($_POST['fathersname']) && is_array($_POST['fathersname']) ? ($_POST['fathersname'][0] ?? '') : ($_POST['fathersname'] ?? '')); ?>" required />
              <?php if (in_array("Father's name is required", $errors ?? [])): ?>
                  <div class="invalid-feedback">Father's name is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Name of Mother (Full Name)</label>
              <input type="text" class="form-control <?php echo in_array("Mother's name is required", $errors ?? []) ? 'is-invalid' : ''; ?>" name="mothersname[]" value="<?php echo htmlspecialchars(isset($_POST['mothersname']) && is_array($_POST['mothersname']) ? ($_POST['mothersname'][0] ?? '') : ($_POST['mothersname'] ?? '')); ?>" required />
              <?php if (in_array("Mother's name is required", $errors ?? [])): ?>
                  <div class="invalid-feedback">Mother's name is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Corpse Disposal and Cemetery Information -->
        <div class="mb-4">
          <h3>Burial/Cremation Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Corpse Disposal</label>
              <select class="form-select <?php echo in_array('Corpse disposal method is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="corpsedisposal[]" required>
                <option value="" disabled <?php echo empty(isset($_POST['corpsedisposal']) && is_array($_POST['corpsedisposal']) ? ($_POST['corpsedisposal'][0] ?? '') : ($_POST['corpsedisposal'] ?? '')) ? 'selected' : ''; ?>>Choose...</option>
                <option value="Burial" <?php echo (isset($_POST['corpsedisposal']) && is_array($_POST['corpsedisposal']) ? ($_POST['corpsedisposal'][0] ?? '') : ($_POST['corpsedisposal'] ?? '')) === 'Burial' ? 'selected' : ''; ?>>Burial</option>
                <option value="Cremation" <?php echo (isset($_POST['corpsedisposal']) && is_array($_POST['corpsedisposal']) ? ($_POST['corpsedisposal'][0] ?? '') : ($_POST['corpsedisposal'] ?? '')) === 'Cremation' ? 'selected' : ''; ?>>Cremation</option>
              </select>
              <?php if (in_array('Corpse disposal method is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Corpse disposal method is required.</div>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Name and Address of Cemetery or Crematory</label>
              <input type="text" class="form-control <?php echo in_array('Cemetery address is required', $errors ?? []) ? 'is-invalid' : ''; ?>" name="cemeteryaddress[]" value="<?php echo htmlspecialchars(isset($_POST['cemeteryaddress']) && is_array($_POST['cemeteryaddress']) ? ($_POST['cemeteryaddress'][0] ?? '') : ($_POST['cemeteryaddress'] ?? '')); ?>" required />
              <?php if (in_array('Cemetery address is required', $errors ?? [])): ?>
                  <div class="invalid-feedback">Cemetery address is required.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
          <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">NEXT</button>
        </div>
      </form>
    </div>
  </main>

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Are you still requesting for yourself?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="handleYes()">YES</button>
          <button type="button" class="btn btn-secondary" onclick="handleNo()">NO</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Checklist Modal -->
  <div class="modal fade checklist-modal" id="checklistModal" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="checklistModalLabel">Document Requirements</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="checklist-header">
            Please check the following requirements. You cannot proceed if any requirement is incomplete.
          </div>
          <div class="alert alert-warning" id="checklistWarning" style="display: none;">
            <strong>Warning:</strong> You must select at least one ID to proceed.
          </div>
          <div class="mb-3">
            <div class="fw-bold mb-2">✓ The ID's that are available to you</div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="nationalId">
                  <label class="form-check-label" for="nationalId">National ID</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="driversLicense">
                  <label class="form-check-label" for="driversLicense">Driver's License</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="umidId">
                  <label class="form-check-label" for="umidId">UMID ID</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="tinId">
                  <label class="form-check-label" for="tinId">TIN ID</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="votersCert">
                  <label class="form-check-label" for="votersCert">Voter's Certificate</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="philhealth">
                  <label class="form-check-label" for="philhealth">PhilHealth</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="prcId">
                  <label class="form-check-label" for="prcId">PRC ID</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="owwaId">
                  <label class="form-check-label" for="owwaId">OWWA ID</label>
                </div>
                <div class="checklist-item">
                  <input class="form-check-input" type="checkbox" id="seniorId">
                  <label class="form-check-label" for="seniorId">Senior Citizen ID</label>
                </div>
              </div>
            </div>
          </div>
          
          <div class="d-flex justify-content-center mt-4">
            <button type="button" class="proceed-btn" id="modalProceedBtn" onclick="proceedToNextStep()" disabled>PROCEED</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JavaScript Logic -->
  <script>
    let certCountYes = 1;
    
    // Add event listeners to all checkboxes in checklist
    document.addEventListener('DOMContentLoaded', function() {
      const allCheckboxes = document.querySelectorAll('#checklistModal input[type="checkbox"]');
      allCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', validateChecklist);
      });

      // Add age calculation listeners to initial form
      addAgeCalculationListeners(document);
    });

    // Function to calculate age at death
    function calculateAge(birthDate, deathDate) {
      if (!birthDate || !deathDate) return '';
      
      const birth = new Date(birthDate);
      const death = new Date(deathDate);
      
      // Validate dates
      if (birth > death) {
        return 0; // Birth date cannot be after death date
      }
      
      let age = death.getFullYear() - birth.getFullYear();
      const monthDiff = death.getMonth() - birth.getMonth();
      
      // Adjust age if death occurred before birthday in the death year
      if (monthDiff < 0 || (monthDiff === 0 && death.getDate() < birth.getDate())) {
        age--;
      }
      
      return Math.max(0, age); // Ensure age is not negative
    }

    // Function to add age calculation listeners to a container
    function addAgeCalculationListeners(container) {
      const birthInputs = container.querySelectorAll('input[name="birthdate[]"]');
      const deathInputs = container.querySelectorAll('input[name="deathdate[]"]');
      const ageInputs = container.querySelectorAll('input[name="age[]"]');

      // Add listeners to both birth and death date inputs
      birthInputs.forEach((birthInput, index) => {
        const deathInput = deathInputs[index];
        const ageInput = ageInputs[index];

        if (birthInput && deathInput && ageInput) {
          // Calculate age when birth date changes
          birthInput.addEventListener('change', function() {
            const age = calculateAge(birthInput.value, deathInput.value);
            if (age !== '') {
              ageInput.value = age;
              // Validate date logic
              if (age < 0 || new Date(birthInput.value) > new Date(deathInput.value)) {
                showDateError(birthInput, 'Birth date cannot be after death date');
                ageInput.value = 0;
              } else {
                clearDateError(birthInput);
              }
            }
          });

          // Calculate age when death date changes
          deathInput.addEventListener('change', function() {
            const age = calculateAge(birthInput.value, deathInput.value);
            if (age !== '') {
              ageInput.value = age;
              // Validate date logic
              if (age < 0 || new Date(birthInput.value) > new Date(deathInput.value)) {
                showDateError(deathInput, 'Death date cannot be before birth date');
                ageInput.value = 0;
              } else {
                clearDateError(deathInput);
                clearDateError(birthInput);
              }
            }
          });
        }
      });
    }

    // Function to show date validation error
    function showDateError(input, message) {
      input.classList.add('is-invalid');
      
      // Remove existing error message
      const existingError = input.parentNode.querySelector('.invalid-feedback');
      if (existingError) {
        existingError.remove();
      }
      
      // Add new error message
      const errorDiv = document.createElement('div');
      errorDiv.className = 'invalid-feedback';
      errorDiv.textContent = message;
      input.parentNode.appendChild(errorDiv);
    }

    // Function to clear date validation error
    function clearDateError(input) {
      input.classList.remove('is-invalid');
      const errorDiv = input.parentNode.querySelector('.invalid-feedback');
      if (errorDiv) {
        errorDiv.remove();
      }
    }
    
    function validateAndShowChecklist(btn) {
      const form = btn.closest('form');
      
      // Enhanced client-side validation
      let isValid = true;
      const requiredFields = form.querySelectorAll('input[required], select[required]');
      
      // Remove existing validation classes
      form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
      form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          const feedback = document.createElement('div');
          feedback.className = 'invalid-feedback';
          feedback.textContent = field.labels[0].textContent.replace(' *', '') + ' is required.';
          field.parentNode.appendChild(feedback);
          isValid = false;
        }
      });
      
      // Check for date validation errors
      const invalidInputs = form.querySelectorAll('.is-invalid');
      if (invalidInputs.length > 0) {
        isValid = false;
      }
      
      if (!isValid) {
        // Scroll to first invalid field
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstInvalid.focus();
        }
        return;
      }
      
      // If validation passes, show checklist modal
      const checklistModal = new bootstrap.Modal(document.getElementById('checklistModal'));
      checklistModal.show();

      // Reset checklist state
      const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
      idCheckboxes.forEach(checkbox => checkbox.checked = false);

      // Reset proceed button state
      const proceedBtn = document.getElementById('modalProceedBtn');
      if (proceedBtn) {
        proceedBtn.disabled = true;
      }
      
      const warningElement = document.getElementById('checklistWarning');
      if (warningElement) {
        warningElement.style.display = 'block';
      }

      // Store reference to the current form for submission
      window.currentDeathForm = form;
    }
    
    // Function to validate the checklist
    function validateChecklist() {
      const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
      const anyIdSelected = Array.from(idCheckboxes).some(checkbox => checkbox.checked);
      const warningElement = document.getElementById('checklistWarning');
      const proceedButton = document.getElementById('modalProceedBtn');
      
      if (anyIdSelected) {
        proceedButton.disabled = false;
        warningElement.style.display = 'none';
      } else {
        proceedButton.disabled = true;
        warningElement.style.display = 'block';
      }
    }
    
    // Function to proceed after checklist verification
    function proceedToNextStep() {
      const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
      const anyIdSelected = Array.from(idCheckboxes).some(checkbox => checkbox.checked);

      if (anyIdSelected) {
        const checklistModal = bootstrap.Modal.getInstance(document.getElementById('checklistModal'));
        checklistModal.hide();

        // Find and submit the form properly
        const form = document.getElementById('deathCertForm');
        if (form) {
            console.log('Submitting death certificate form...');
            form.submit();
        } else {
            console.error('Form not found!');
        }
      } else {
        // Show visual indicators for missing requirements
        const warningElement = document.getElementById('checklistWarning');
        
        // Show warning with animation
        warningElement.style.display = 'block';
        warningElement.classList.add('animate__animated', 'animate__shakeX');
        setTimeout(() => {
            warningElement.classList.remove('animate__animated', 'animate__shakeX');
        }, 1000);
      }
    }

    function showConfirmation() {
      const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
      modal.show();
    }

    function handleYes() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
      modal.hide();
      cloneFieldsYes();
    }

    function handleNo() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
      modal.hide();
      addDifferentRequester();
    }

    function cloneFieldsYes() {
      const container = document.querySelector("main");
      const allCerts = document.querySelectorAll(".form-box");
      const firstCert = allCerts[0]; // Always clone data from Cert #1
      const lastCert = allCerts[allCerts.length - 1];

      const oldBtns = lastCert.querySelector(".d-flex.justify-content-between");
      if (oldBtns) oldBtns.remove();

      const newCert = lastCert.cloneNode(true); // Clone layout from lastCert
      certCountYes++;

      const form = newCert.querySelector("form");
      form.className = "death-form";
      form.id = `deathCertForm${certCountYes}`;
      form.querySelector("h3").textContent = `Certificate #${certCountYes}`;

      // Copy values from first certificate into newCert
      const firstInputs = firstCert.querySelectorAll("input, select");
      const newInputs = newCert.querySelectorAll("input, select");

      firstInputs.forEach((input, i) => {
        if (input.type !== "button" && input.type !== "submit") {
          newInputs[i].value = input.value;
        }
      });

      // Remove any existing remove button to avoid duplicates
      const oldRemoveBtn = newCert.querySelector(".btn-danger");
      if (oldRemoveBtn) oldRemoveBtn.remove();

      // Clear any validation errors from the cloned form
      const invalidInputs = newCert.querySelectorAll('.is-invalid');
      invalidInputs.forEach(input => input.classList.remove('is-invalid'));
      const errorMessages = newCert.querySelectorAll('.invalid-feedback');
      errorMessages.forEach(msg => msg.remove());

      // Add age calculation listeners to the new form
      addAgeCalculationListeners(newCert);

      // Add action buttons
      const btnGroup = document.createElement("div");
      btnGroup.className = "d-flex justify-content-between mt-4";
      btnGroup.innerHTML = `
        <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
        <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">NEXT</button>
      `;
      form.appendChild(btnGroup);

      // Add a new clean remove button
      const removeBtn = document.createElement("button");
      removeBtn.className = "btn btn-danger mt-3";
      removeBtn.type = "button";
      removeBtn.textContent = "Remove Certificate";
      removeBtn.onclick = () => {
        newCert.remove();
        certCountYes--;
        restoreButtonsToLastCert();
      };
      form.appendChild(removeBtn);

      container.appendChild(newCert);
    }

    function restoreButtonsToLastCert() {
      const allCerts = document.querySelectorAll(".form-box");
      if (allCerts.length === 0) return;

      const lastCert = allCerts[allCerts.length - 1];
      const form = lastCert.querySelector("form");

      // Only restore if not already present
      if (!form.querySelector(".btn-add")) {
        const btnGroup = document.createElement("div");
        btnGroup.className = "d-flex justify-content-between mt-4";
        btnGroup.innerHTML = `
          <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">NEXT</button>
        `;
        form.appendChild(btnGroup);
      }
    }

    function addDifferentRequester() {
      window.location.href = 'death.php?different_requester=yes';
    }
  </script>
</body>
</html>