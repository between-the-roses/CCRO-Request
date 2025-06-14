<?php
include "../backend/db.php";
include "includes/navbar.php";

if (!$conn) {
  echo "<div class='alert alert-danger mt-3'>Database connection failed.</div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form data
    $fullname = trim($_POST['child_firstname'] . ' ' . $_POST['child_middlename'] . ' ' . $_POST['child_lastname']);
    $contactno = trim($_POST['contactno']);
    $address = trim($_POST['address']);
    $relationship = trim($_POST['relationship']);
    $purpose = trim($_POST['purpose']);
    if ($purpose === 'Other') {
        $purpose = trim($_POST['purpose_other']);
    }
    $email_address = trim($_POST['email_address']);
    $registryno = trim($_POST['registryno']);
    $copies = intval($_POST['copies']);
    $civilstatus = trim($_POST['civilstatus']);

    // Child info
    $child_firstname = trim($_POST['child_firstname']);
    $child_middlename = trim($_POST['child_middlename']);
    $child_lastname = trim($_POST['child_lastname']);
    $birthdate = $_POST['birthdate'];
    $birthplace = trim($_POST['birthplace']);

    // Father's info
    $fathersname = trim($_POST['father_firstname'] . ' ' . $_POST['father_middlename'] . ' ' . $_POST['father_lastname']);

    // Mother's info
    $mothersname = trim($_POST['mother_firstname'] . ' ' . $_POST['mother_middlename'] . ' ' . $_POST['mother_lastname']);

    try {
        // 1. Insert into customer table
        $stmt = $conn->prepare("INSERT INTO customer (fullname, contactno, address, relationship, civilstatus, purpose, copies, certificate_type, email_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING customer_id");
        $stmt->execute([$fullname, $contactno, $address, $relationship, $civilstatus, $purpose, $copies, 'livebirth', $email_address]);
        $customer_id = $stmt->fetch(PDO::FETCH_ASSOC)['customer_id'];

        // 2. Insert into registry table (if registryno is provided)
        $registry_id = null;
        if (!empty($registryno)) {
            $stmt2 = $conn->prepare("INSERT INTO registry (registryno) VALUES (?) RETURNING registry_id");
            $stmt2->execute([$registryno]);
            $registry_id = $stmt2->fetch(PDO::FETCH_ASSOC)['registry_id'];
        }

        // 3. Insert into birth table (use registry_id if available)
        $stmt3 = $conn->prepare("INSERT INTO birth (customer_id, registry_id, childinfo, birthdate, birthplace, fathersname, mothersname) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->execute([
            $customer_id,
            $registry_id,
            "{$child_firstname} {$child_middlename} {$child_lastname}",
            $birthdate,
            $birthplace,
            $fathersname,
            $mothersname
        ]);

        // Success message styled at the upper right corner
        echo "<div style='position:fixed;top:30px;right:30px;z-index:2000;min-width:300px;' class='alert alert-success shadow'>Birth certificate request submitted successfully!</div>";
        // Redirect to the next step or confirmation page
        echo "<script>setTimeout(function(){ window.location.href = 'http://localhost/Thesis-/UserDashboard/verification.php; }, 2000);</script>";
      } catch (PDOException $e) {
        // Error message styled at the upper right corner
        echo "<div style='position:fixed;top:30px;right:30px;z-index:2000;min-width:300px;' class='alert alert-danger shadow'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
          }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Live Birth Certificate Form</title>

  <!-- Bootstrap & Boxicons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

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

  <!-- Main Content -->
  <main class="container">
    <h1 class="mb-4 text-center text-lg-start">Birth Certificate Details</h1>

    <!-- First Certificate -->
    <div class="form-box">
      <form class="birth-form" id="birthCertForm" method="POST">
        <h4 class="mt-3 mb-3 fw-bold">Requester's Information</h4>

        <div class="row g-3">
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
            <select class="form-select" name="relationship" required>
              <option value="SELF" selected>SELF</option>
              <option value="Parent">Parent</option>
              <option value="Guardian">Guardian</option>
              <option value="Relative">Relative</option>
              <option value="Others">Others</option>
            </select>
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
            <label class="form-label required">Home Address</label>
            <input type="text" class="form-control" name="address" required />
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
        </div><br /><hr />

        <h3>Certificate #1</h3>
        <!-- Certificate Info -->
        <div class="mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Registry Number (Optional)</label>
              <input type="text" class="form-control" name="registryno" />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Number of Copies</label>
              <select class="form-select" name="copies" required>
                <option selected disabled>Choose...</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Child Info -->
        <div class="mb-4">
          <h3>Person/Child Information</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="child_firstname" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="child_middlename" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="child_lastname" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Date of Birth</label>
              <input type="date" class="form-control" name="birthdate" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Place of Birth</label>
              <input type="text" class="form-control" name="birthplace" required />
            </div>
          </div>
        </div>

        <!-- Father's Info -->
        <div class="mb-4">
          <h3>Complete Name of the Father</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="father_firstname" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="father_middlename" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="father_lastname" required />
            </div>
          </div>
        </div>

        <!-- Mother's Info -->
        <div class="mb-4">
          <h3>Complete Name of the Mother</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="mother_firstname" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="mother_middlename" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="mother_lastname" required />
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
          <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist()">NEXT</button>
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
            <button type="button" class="proceed-btn" onclick="proceedToNextStep()" disabled>PROCEED</button>
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
    });
    
    // Function to validate the checklist
    function validateChecklist() {
      const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
      const anyIdSelected = Array.from(idCheckboxes).some(checkbox => checkbox.checked);
      const warningElement = document.getElementById('checklistWarning');
      const proceedButton = document.querySelector('.proceed-btn');
      
      if (anyIdSelected) {
        // Enable the proceed button and hide warning
        proceedButton.disabled = false;
        warningElement.style.display = 'none';
      } else {
        // Disable the proceed button and show warning
        proceedButton.disabled = true;
        warningElement.style.display = 'block';
      }
    }
    
    // Function to validate the form before showing checklist
    function validateAndShowChecklist() {
      const form = document.getElementById('birthCertForm');
      
      // Add 'was-validated' class to show validation feedback
      form.classList.add('was-validated');
      
      // Check if form is valid
      if (form.checkValidity()) {
        // If valid, show checklist modal
        const checklistModal = new bootstrap.Modal(document.getElementById('checklistModal'));
        checklistModal.show();
        
        // Reset checklist state
        const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
        idCheckboxes.forEach(checkbox => checkbox.checked = false);
        
        // Ensure the proceed button is disabled initially
        document.querySelector('.proceed-btn').disabled = true;
        document.getElementById('checklistWarning').style.display = 'block';
        
      } else {
        // If not valid, scroll to the first invalid field
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) {
          firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    }
    
    // Function to proceed after checklist verification
    function proceedToNextStep() {
      // Double-check if at least one ID is selected
      const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
      const anyIdSelected = Array.from(idCheckboxes).some(checkbox => checkbox.checked);
      
      if (anyIdSelected) {
        // Hide the modal
        const checklistModal = bootstrap.Modal.getInstance(document.getElementById('checklistModal'));
        checklistModal.hide();

        // Redirect immediately to verification page
        window.location.href = 'http://localhost/Thesis-/UserDashboard/verification.php';
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
        idSection.classList.add('text-danger');
        setTimeout(() => idSection.classList.remove('text-danger'), 3000);
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
      form.className = "birth-form";
      form.id = `birthCertForm${certCountYes}`;
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

      // Add action buttons
      const btnGroup = document.createElement("div");
      btnGroup.className = "d-flex justify-content-between mt-4";
      btnGroup.innerHTML = `
        <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
        <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist()">NEXT</button>
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
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist()">NEXT</button>
        `;
        form.appendChild(btnGroup);
      }
    }

    function addDifferentRequester() {
      const container = document.querySelector("main");
      certCountYes++;

      const formBox = document.createElement("div");
      formBox.className = "form-box";

      formBox.innerHTML = `
        <form class="birth-form" id="birthCertForm${certCountYes}">
          <h3>Certificate #${certCountYes}</h3>
          <h4>Complete name of the Requesting Party</h4>
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" required />
            </div>
            <div class="col-12">
              <label class="form-label required">Address</label>
              <input type="text" class="form-control" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Relationship to the Child</label>
              <select class="form-select" required>
                <option selected disabled>Choose...</option>
                <option>Parent</option>
                <option>Guardian</option>
                <option>Relative</option>
                <option>Others</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label required">Purpose of the Certification</label>
              <select class="form-select" required>
                <option selected disabled>Choose...</option>
                <option>School</option>
                <option>Travel</option>
                <option>Legal</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label required">Upload Authorization Letter</label>
              <input type="file" class="form-control" required />
            </div>
          </div>

          <!-- Registry Number & Copies -->
          <hr class="my-4" />
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Registry Number (Optional)</label>
              <input type="text" class="form-control" />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Number of Copies</label>
              <select class="form-select" required>
                <option selected disabled>Choose...</option>
                <option>1</option>
                <option>2</option>
                <option>3</option>
              </select>
            </div>
          </div>

          <!-- Person/Child Information -->
          <div class="mb-4">
            <h3>Person/Child Information</h3>
            <div class="row g-3">
              <div class="col-md-4"><label class="form-label required">First Name</label><input type="text" class="form-control" required /></div>
              <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label required">Last Name</label><input type="text" class="form-control" required /></div>
              <div class="col-md-6"><label class="form-label required">Date of Birth</label><input type="date" class="form-control" required /></div>
              <div class="col-md-6"><label class="form-label required">Place of Birth</label><input type="text" class="form-control" required /></div>
            </div>
          </div>

          <!-- Complete Name of the Father -->
          <div class="mb-4">
            <h3>Complete Name of the Father</h3>
            <div class="row g-3">
              <div class="col-md-4"><label class="form-label required">First Name</label><input type="text" class="form-control" required /></div>
              <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label required">Last Name</label><input type="text" class="form-control" required /></div>
            </div>
          </div>

          <!-- Complete Name of the Mother -->
          <div class="mb-4">
            <h3>Complete Name of the Mother</h3>
            <div class="row g-3">
              <div class="col-md-4"><label class="form-label required">First Name</label><input type="text" class="form-control" required /></div>
              <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" /></div>
              <div class="col-md-4"><label class="form-label required">Last Name</label><input type="text" class="form-control" required /></div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">Add Another Certificate</button>
            <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist()">NEXT</button>
          </div>

          <button type="button" class="btn btn-danger mt-3" onclick="this.closest('.form-box').remove(); certCountYes--; restoreButtonsToLastCert();">Remove Certificate</button>
        </form>
      `;

      // Remove buttons in the previous (last) cert
      const lastCert = document.querySelectorAll(".form-box")[document.querySelectorAll(".form-box").length - 1];
      const oldBtns = lastCert.querySelector(".d-flex.justify-content-between");
      if (oldBtns) oldBtns.remove();

      // Add the new certificate
      container.appendChild(formBox);
    }

    function toggleOtherPurpose(selectElement) {
      const otherInput = document.getElementById('purposeOtherInput');
      if (selectElement.value === 'Other') {
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