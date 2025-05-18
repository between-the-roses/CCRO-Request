<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Death Certificate - Not Requesting for Self</title>

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
    <h1 class="mb-4 text-center text-lg-start">Death Certificate Details</h1>

    <!-- Initial Certificate -->
    <div class="form-box">
      <form class="death-form" id="deathCertForm">
        <h3>Certificate #1</h3>
        <h4 class="mt-3 mb-3">Complete Name of the Requesting Party</h4>


        <div class="row g-3">
          <div class="col-md-4"><label class="form-label required">First Name</label><input type="text" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" /></div>
          <div class="col-md-4"><label class="form-label required">Last Name</label><input type="text" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label required">Address</label><input type="text" class="form-control" required /></div>
          <div class="col-md-3"><label class="form-label required">Relationship to the Deceased</label>
            <select class="form-select" required>
              <option disabled selected>Relationship</option>
              <option>Spouse</option>
              <option>Parent</option>
              <option>Child</option>
              <option>Sibling</option>
              <option>Guardian</option>
              <option>Relative</option>
              <option>Legal Representative</option>
            </select>
          </div>
          <div class="col-md-3"><label class="form-label required">Purpose of the Certification</label>
            <select class="form-select" required>
              <option disabled selected>Purpose</option>
              <option>Insurance Claim</option>
              <option>Estate Settlement</option>
              <option>Legal</option>
              <option>Benefits Claim</option>
              <option>Other</option>
            </select>
          </div>
          <div class="col-md-12"><label class="form-label required">Upload Authorization Letter</label><input type="file" class="form-control" required /></div>
        </div>

        <hr class="my-4" />
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Registry Number (Optional)</label><input type="text" class="form-control" /></div>
          <div class="col-md-6"><label class="form-label required">Number of Copies</label>
            <select class="form-select" required>
              <option disabled selected>Choose...</option>
              <option>1</option>
              <option>2</option>
              <option>3</option>
            </select>
          </div>
        </div>

        <hr class="my-4" />
        <h4>Deceased Person Information</h4>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label required">First Name</label><input type="text" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label">Middle Name</label><input type="text" class="form-control" /></div>
          <div class="col-md-4"><label class="form-label required">Last Name</label><input type="text" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Date of Birth</label><input type="date" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Date of Death</label><input type="date" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Age</label><input type="number" min="0" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Sex</label>
            <select class="form-select" required>
              <option disabled selected>Choose...</option>
              <option>Male</option>
              <option>Female</option>
            </select>
          </div>
          <div class="col-md-8"><label class="form-label required">Place of Death</label><input type="text" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Civil Status</label>
            <select class="form-select" required>
              <option disabled selected>Choose...</option>
              <option>Single</option>
              <option>Married</option>
              <option>Widowed</option>
              <option>Divorced</option>
              <option>Separated</option>
            </select>
          </div>
          <div class="col-md-4"><label class="form-label required">Religion</label><input type="text" class="form-control" required /></div>
          <div class="col-md-4"><label class="form-label required">Citizenship</label><input type="text" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label required">Residence</label><input type="text" class="form-control" required /></div>
          <div class="col-md-6"><label class="form-label required">Occupation</label><input type="text" class="form-control" required /></div>
        </div>

        <!-- Buttons -->
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
          <h5 class="modal-title" id="confirmModalLabel">Are you requesting for yourself?</h5>
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
    let certCountNo = 1;
    
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
      const form = document.getElementById('deathCertForm');
      
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
        
        // Submit the form
        document.getElementById('deathCertForm').submit();
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
      window.location.href = 'deathyes.html'; // Redirect to the "requesting for self" page
    }

    function handleNo() {
      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
      modal.hide();
      cloneFieldsNo();
    }

    function cloneFieldsNo() {
      const container = document.querySelector("main");
      const allCerts = document.querySelectorAll(".form-box");
      const firstCert = allCerts[0]; // Always clone data from Cert #1
      const lastCert = allCerts[allCerts.length - 1];

      const oldBtns = lastCert.querySelector(".d-flex.justify-content-between");
      if (oldBtns) oldBtns.remove();

      const newCert = lastCert.cloneNode(true); // Clone layout from lastCert
      certCountNo++;

      const form = newCert.querySelector("form");
      form.className = "death-form";
      form.id = `deathCertForm${certCountNo}`;
      form.querySelector("h3").textContent = `Certificate #${certCountNo}`;

      // Reset the form fields (except for requesting party information)
      const requesterSection = newCert.querySelector("h4.mt-3.mb-3").nextElementSibling;
      const deceasedSection = newCert.querySelector("h4:not(.mt-3)").parentElement;
      
      // Keep requester info from first cert
      const firstRequesterInputs = firstCert.querySelectorAll("h4.mt-3.mb-3 + div input, h4.mt-3.mb-3 + div select");
      const newRequesterInputs = newCert.querySelectorAll("h4.mt-3.mb-3 + div input, h4.mt-3.mb-3 + div select");
      
      firstRequesterInputs.forEach((input, i) => {
        if (input.type !== "button" && input.type !== "submit") {
          newRequesterInputs[i].value = input.value;
        }
      });
      
      // Clear deceased person info
      const deceasedInputs = newCert.querySelectorAll("h4:not(.mt-3) + div input, h4:not(.mt-3) + div select");
      deceasedInputs.forEach(input => {
        if (input.type === "select-one") {
          input.selectedIndex = 0;
        } else if (input.type !== "button" && input.type !== "submit") {
          input.value = "";
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
        certCountNo--;
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
  </script>
</body>
</html>