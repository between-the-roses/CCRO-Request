<style>
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
  cursor: pointer;
  transition: all 0.3s ease;
}

.checklist-modal .proceed-btn:hover:not(:disabled) {
  background-color: #0ea5e9;
  transform: scale(1.05);
}

.checklist-modal .proceed-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
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

<script>
// Initialize checklist on page load
document.addEventListener('DOMContentLoaded', function() {
  const allCheckboxes = document.querySelectorAll('#checklistModal input[type="checkbox"]');
  allCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', validateChecklist);
  });
});

// Show checklist modal with validation
function validateAndShowChecklist(btn) {
  const form = btn.closest('form');
  
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
    window.currentForm = form;
  } else {
    // Scroll to first invalid field
    const firstInvalid = form.querySelector(':invalid');
    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus();
    }
  }
}

// Validate checklist selections
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

// Proceed after checklist verification
function proceedToNextStep() {
  const idCheckboxes = document.querySelectorAll('#checklistModal .col-md-6 .form-check-input');
  const anyIdSelected = Array.from(idCheckboxes).some(checkbox => checkbox.checked);

  if (anyIdSelected) {
    const checklistModal = bootstrap.Modal.getInstance(document.getElementById('checklistModal'));
    checklistModal.hide();

    // Submit the stored form
    if (window.currentForm) {
      window.currentForm.submit();
    } else {
      console.error('No form to submit');
    }
  } else {
    // Show warning animation
    const warningElement = document.getElementById('checklistWarning');
    warningElement.style.display = 'block';
    warningElement.classList.add('animate__animated', 'animate__shakeX');
    setTimeout(() => {
      warningElement.classList.remove('animate__animated', 'animate__shakeX');
    }, 1000);
  }
}
</script>


