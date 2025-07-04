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
        
        echo "<div style='position:fixed;top:30px;right:30px;z-index:2000;min-width:300px;' class='alert alert-success shadow'>Death certificate request submitted successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = '../verification.php'; }, 2000);</script>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $is_multiple = is_array($_POST['deceased_firstname'] ?? null);

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

        // Redirect to verification page
        header("Location: ../verification.php");
        exit;

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
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
      
      <p class="text-center text-lg-start mb-4">
        Please fill out the form below to request a death certificate. Ensure all required fields are completed.
        If you need to add another certificate, click the "Add Another Certificate" button.
      </p>

      <?php
      // Show customer summary (from session) before the death form
      if (isset($_SESSION['customer_data'])) {
        $cd = $_SESSION['customer_data'];
        ?>
        <div class="mb-4 p-3 rounded bg-white border shadow-sm">
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
      <form class="death-form" id="deathCertForm" method="POST">
        <h3>Certificate #1</h3>

        <!-- Certificate Info -->
        <div class="mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Registry Number (Optional)</label>
              <input type="text" class="form-control" name="registry_id[]" />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Number of Copies</label>
              <select class="form-select" name="copies[]" required>
                <option selected disabled>Choose...</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Deceased Person Complete Name -->
        <div class="mb-4">
          <h3>Complete Name of the Deceased</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="deceased_firstname[]" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="deceased_middlename[]" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="deceased_lastname[]" required />
            </div>
          </div>
        </div>

        <!-- Death and Birth Dates -->
        <div class="mb-4">
          <h3>Date Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Date of Death</label>
              <input type="date" class="form-control" name="deathdate[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Date of Birth</label>
              <input type="date" class="form-control" name="birthdate[]" required />
            </div>
          </div>
        </div>

        <!-- Age and Sex -->
        <div class="mb-4">
          <h3>Personal Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Age at Death</label>
              <input type="number" class="form-control" name="age[]" min="0" max="150" required readonly style="background-color: #f8f9fa;" />
              <small class="text-muted">Age will be calculated automatically from birth and death 
                s</small>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Sex</label>
              <select class="form-select" name="sex[]" required>
                <option selected disabled>Choose...</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Place of Death and Civil Status -->
        <div class="mb-4">
          <h3>Death Location & Status</h3>
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label required">Place of Death (City/Municipality/Province)</label>
              <input type="text" class="form-control" name="deathplace[]" required />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Civil Status</label>
              <select class="form-select" name="civilstatus_deceased[]" required>
                <option selected disabled>Choose...</option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Divorced">Divorced</option>
                <option value="Separated">Separated</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Religion, Citizenship, Residence, Occupation -->
        <div class="mb-4">
          <h3>Additional Details</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Religion</label>
              <input type="text" class="form-control" name="religion[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Citizenship</label>
              <input type="text" class="form-control" name="citizenship[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Residence</label>
              <input type="text" class="form-control" name="residence[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Occupation</label>
              <input type="text" class="form-control" name="occupation[]" required />
            </div>
          </div>
        </div>

        <!-- Parents Information -->
        <div class="mb-4">
          <h3>Parents Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Name of Father (Full Name)</label>
              <input type="text" class="form-control" name="fathersname[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Name of Mother (Full Name)</label>
              <input type="text" class="form-control" name="mothersname[]" required />
            </div>
          </div>
        </div>

        <!-- Corpse Disposal and Cemetery Information -->
        <div class="mb-4">
          <h3>Burial/Cremation Information</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Corpse Disposal</label>
              <select class="form-select" name="corpsedisposal[]" required>
                <option selected disabled>Choose...</option>
                <option value="Burial">Burial</option>
                <option value="Cremation">Cremation</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Name and Address of Cemetery or Crematory</label>
              <input type="text" class="form-control" name="cemetery_address[]" required />
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
      
      // Check for date validation errors before proceeding
      const invalidInputs = form.querySelectorAll('.is-invalid');
      if (invalidInputs.length > 0) {
        // Scroll to first invalid field
        invalidInputs[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        invalidInputs[0].focus();
        return;
      }
      
      // Add validation styling
      form.classList.add('was-validated');
      
      if (form.checkValidity()) {
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
      } else {
        // Scroll to first invalid field
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
          firstInvalid.focus();
        }
      }
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
            form.submit();
        } else if (window.currentDeathForm) {
            window.currentDeathForm.submit();
        } else {
            // Fallback: create a hidden form to submit
            const hiddenForm = document.createElement('form');
            hiddenForm.method = 'POST';
            hiddenForm.action = '';
            
            // Copy all form data from the visible form
            const visibleForm = document.querySelector('.death-form');
            if (visibleForm) {
                const formData = new FormData(visibleForm);
                for (let [key, value] of formData.entries()) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    hiddenForm.appendChild(input);
                }
            }
            
            document.body.appendChild(hiddenForm);
            hiddenForm.submit();
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
        
        // Highlight ID section
        const idSection = document.querySelector('#checklistModal .fw-bold.mb-2');
        if (idSection) {
            idSection.classList.add('text-danger');
            setTimeout(() => idSection.classList.remove('text-danger'), 3000);
        }
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