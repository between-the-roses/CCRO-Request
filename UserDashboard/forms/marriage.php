<?php
session_start();
include __DIR__ . "/../../backend/db.php";
include "../includes/navbar.php";

// ✅ INCLUDE TRANSACTION.PHP FIRST - BEFORE ANY CODE THAT USES IT
include __DIR__ . "/../transaction.php";

// Check if customer data exists in session
if (!isset($_SESSION['customer_data'])) {
    header("Location: http://localhost/CCRO-Request/UserDashboard/customer.php");
    exit;
}

$error_messages = [];
$success_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_data = $_SESSION['customer_data'];
    $certificate_type = 'marriage';

    try {
        // Validate customer data
        if (empty($customer_data['fullname'])) {
            throw new Exception("Customer full name is missing from session.");
        }
        if (empty($customer_data['contactno'])) {
            throw new Exception("Customer contact number is missing from session.");
        }
        if (empty($customer_data['email_address'])) {
            throw new Exception("Customer email address is missing from session.");
        }

        // Get copies from form data
        $copies = 1;
        if (isset($_POST['copies'])) {
            if (is_array($_POST['copies'])) {
                $copies = intval($_POST['copies'][0]);
            } else {
                $copies = intval($_POST['copies']);
            }
        }

        // Validate copies
        if ($copies < 1 || $copies > 5) {
            throw new Exception("Number of copies must be between 1 and 5.");
        }

        // Validate marriage certificate data
        $is_multiple = is_array($_POST['husband_firstname'] ?? null);
        
        if ($is_multiple) {
            $count = count($_POST['husband_firstname']);
            if ($count === 0) {
                throw new Exception("At least one marriage certificate must be provided.");
            }
            
            for ($i = 0; $i < $count; $i++) {
                if (empty(trim($_POST['husband_firstname'][$i] ?? ''))) {
                    throw new Exception("Husband's first name is required for certificate #" . ($i + 1));
                }
                if (empty(trim($_POST['husband_lastname'][$i] ?? ''))) {
                    throw new Exception("Husband's last name is required for certificate #" . ($i + 1));
                }
                if (empty(trim($_POST['wife_firstname'][$i] ?? ''))) {
                    throw new Exception("Wife's first name is required for certificate #" . ($i + 1));
                }
                if (empty(trim($_POST['wife_lastname'][$i] ?? ''))) {
                    throw new Exception("Wife's last name is required for certificate #" . ($i + 1));
                }
                if (empty($_POST['marriagedate'][$i] ?? '')) {
                    throw new Exception("Marriage date is required for certificate #" . ($i + 1));
                }
                if (empty(trim($_POST['marriageplace'][$i] ?? ''))) {
                    throw new Exception("Marriage place is required for certificate #" . ($i + 1));
                }
                
                // Validate date format
                $marriage_date = DateTime::createFromFormat('Y-m-d', $_POST['marriagedate'][$i]);
                if (!$marriage_date) {
                    throw new Exception("Invalid marriage date format for certificate #" . ($i + 1) . ". Please use YYYY-MM-DD format.");
                }
            }
        } else {
            // Single certificate validation
            if (empty(trim($_POST['husband_firstname'] ?? ''))) {
                throw new Exception("Husband's first name is required.");
            }
            if (empty(trim($_POST['husband_lastname'] ?? ''))) {
                throw new Exception("Husband's last name is required.");
            }
            if (empty(trim($_POST['wife_firstname'] ?? ''))) {
                throw new Exception("Wife's first name is required.");
            }
            if (empty(trim($_POST['wife_lastname'] ?? ''))) {
                throw new Exception("Wife's last name is required.");
            }
            if (empty($_POST['marriagedate'] ?? '')) {
                throw new Exception("Marriage date is required.");
            }
            if (empty(trim($_POST['marriageplace'] ?? ''))) {
                throw new Exception("Marriage place is required.");
            }
            
            // Validate date format
            $marriage_date = DateTime::createFromFormat('Y-m-d', $_POST['marriagedate']);
            if (!$marriage_date) {
                throw new Exception("Invalid marriage date format. Please use YYYY-MM-DD format.");
            }
        }

        // Start transaction
        $conn->beginTransaction();

        // Insert customer first
        $stmt = $conn->prepare("
            INSERT INTO customer (fullname, contactno, address, relationship, civilstatus, purpose, copies, certificate_type, email_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if (!$stmt->execute([
            $customer_data['fullname'],
            $customer_data['contactno'],
            $customer_data['address'] ?? '',
            $customer_data['relationship'] ?? '',
            $customer_data['civilstatus'] ?? '',
            $customer_data['purpose'] ?? '',
            $copies,
            $certificate_type,
            $customer_data['email_address']
        ])) {
            throw new Exception("Failed to insert customer data: " . implode(", ", $stmt->errorInfo()));
        }

        $customer_id = $conn->lastInsertId();
        
        if (!$customer_id) {
            throw new Exception("Failed to retrieve customer ID after insertion.");
        }

        // ✅ NOW the function EXISTS because we included it at the top
        if (!function_exists('generateTransactionFromId')) {
            throw new Exception("Function 'generateTransactionFromId' not found. Check if transaction.php is properly included.");
        }
        
        $transaction_number = generateTransactionFromId($customer_id);
        
        if (empty($transaction_number)) {
            throw new Exception("Failed to generate transaction number.");
        }

        // Insert into transaction table
        $stmt = $conn->prepare("
            INSERT INTO transaction (customer_id, transaction_no, transaction_status, transactiontype, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        if (!$stmt->execute([
            $customer_id,
            $transaction_number,
            'pending',              // ✅ FIXED: Removed extra values, use proper enum value
            $certificate_type
        ])) {
            throw new Exception("Failed to insert transaction record: " . implode(", ", $stmt->errorInfo()));
        }

        $transaction_id = $conn->lastInsertId();
        
        if (!$transaction_id) {
            throw new Exception("Failed to retrieve transaction ID after insertion.");
        }

        // Handle marriage certificates
        if ($is_multiple) {
            $count = count($_POST['husband_firstname']);
            
            for ($i = 0; $i < $count; $i++) {
                $registry_id = trim($_POST['registry_id'][$i] ?? '');
                $registry_id = ($registry_id === '') ? null : intval($registry_id);

                $husbandname = trim(
                    ($_POST['husband_firstname'][$i] ?? '') . ' ' . 
                    ($_POST['husband_middlename'][$i] ?? '') . ' ' . 
                    ($_POST['husband_lastname'][$i] ?? '')
                );
                $husbandname = preg_replace('/\s+/', ' ', $husbandname);
                
                $wifename = trim(
                    ($_POST['wife_firstname'][$i] ?? '') . ' ' . 
                    ($_POST['wife_middlename'][$i] ?? '') . ' ' . 
                    ($_POST['wife_lastname'][$i] ?? '')
                );
                $wifename = preg_replace('/\s+/', ' ', $wifename);
                
                $marriagedate = $_POST['marriagedate'][$i] ?? '';
                $marriageplace = trim($_POST['marriageplace'][$i] ?? '');

                // ✅ FIXED: Removed created_at since customer table doesn't have it
                $stmt = $conn->prepare("
                    INSERT INTO marriage (customer_id, registry_id, husbandname, wifename, marriagedate, marriageplace) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                if (!$stmt->execute([$customer_id, $registry_id, $husbandname, $wifename, $marriagedate, $marriageplace])) {
                    throw new Exception("Failed to insert marriage certificate #" . ($i + 1) . ": " . implode(", ", $stmt->errorInfo()));
                }
            }
        } else {
            // Single certificate
            $registry_id = trim($_POST['registry_id'] ?? '');
            $registry_id = ($registry_id === '') ? null : intval($registry_id);

            $husbandname = trim(
                ($_POST['husband_firstname'] ?? '') . ' ' . 
                ($_POST['husband_middlename'] ?? '') . ' ' . 
                ($_POST['husband_lastname'] ?? '')
            );
            $husbandname = preg_replace('/\s+/', ' ', $husbandname);
            
            $wifename = trim(
                ($_POST['wife_firstname'] ?? '') . ' ' . 
                ($_POST['wife_middlename'] ?? '') . ' ' . 
                ($_POST['wife_lastname'] ?? '')
            );
            $wifename = preg_replace('/\s+/', ' ', $wifename);
            
            $marriagedate = $_POST['marriagedate'] ?? '';
            $marriageplace = trim($_POST['marriageplace'] ?? '');

            // ✅ FIXED: Changed createdat to created_at AND removed NOW() since not needed
            $stmt = $conn->prepare("
                INSERT INTO marriage (customer_id, registry_id, husbandname, wifename, marriagedate, marriageplace) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if (!$stmt->execute([$customer_id, $registry_id, $husbandname, $wifename, $marriagedate, $marriageplace])) {
                throw new Exception("Failed to insert marriage certificate: " . implode(", ", $stmt->errorInfo()));
            }
        }

        // Commit transaction
        $conn->commit();

        // Clear customer data but keep transaction info
        unset($_SESSION['customer_data']);
        $_SESSION['customer_id'] = $customer_id;
        $_SESSION['transaction_number'] = $transaction_number;
        $_SESSION['transaction_id'] = $transaction_id;

        // Show success message and redirect
        echo "<div style='position:fixed;top:30px;right:30px;z-index:2000;min-width:300px;' class='alert alert-success shadow'><i class='fas fa-check-circle me-2'></i>Marriage certificate request submitted successfully!</div>";
        echo "<script>setTimeout(function(){ window.location.href = '../verification.php'; }, 2000);</script>";
        exit;

    } catch (Exception $e) {
        // Rollback transaction if it was started
        try {
            $conn->rollBack();
        } catch (Exception $rollback_error) {
            error_log("Rollback error: " . $rollback_error->getMessage());
        }
        
        $error_messages[] = $e->getMessage();
        error_log("Marriage certificate error: " . $e->getMessage());
    } catch (PDOException $e) {
        // Rollback transaction if it was started
        try {
            $conn->rollBack();
        } catch (Exception $rollback_error) {
            error_log("Rollback error: " . $rollback_error->getMessage());
        }
        
        $error_messages[] = "Database error: " . $e->getMessage();
        error_log("Database error in marriage.php: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Marriage Certificate Form</title>

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

    .error-alert strong {
      display: block;
      margin-bottom: 8px;
    }

    .error-alert ul {
      margin-bottom: 0;
      padding-left: 20px;
    }

    .error-alert li {
      margin-bottom: 5px;
    }

    .success-alert {
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      color: #155724;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <main class="container">
    <div class="form-box">
      <h1 class="mb-4 text-center text-lg-start">Marriage Certificate Details</h1>
      
      <!-- Error Messages -->
      <?php if (!empty($error_messages)): ?>
        <div class="error-alert">
          <strong><i class="fas fa-exclamation-triangle me-2"></i>Error(s) Occurred:</strong>
          <ul>
            <?php foreach ($error_messages as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Success Message -->
      <?php if ($success_message): ?>
        <div class="success-alert">
          <i class="fas fa-check-circle me-2"></i>
          <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>

      <!-- Transaction Number Display -->
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
        Please fill out the form below to request a marriage certificate. Ensure all required fields are completed.
        If you need to add another certificate, click the "Add Another Certificate" button.
      </p>

      <!-- Customer Information Display -->
      <?php
      if (isset($_SESSION['customer_data'])) {
        $cd = $_SESSION['customer_data'];
        ?>
        <div class="mb-4 p-3 rounded bg-white border shadow-sm">
          <h3 class="mb-3">Requester's Information</h3>
          <dl class="row mb-0">
            <dt class="col-sm-4">Full Name</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['fullname'] ?? 'N/A'); ?></dd>

            <dt class="col-sm-4">Contact No.</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['contactno'] ?? 'N/A'); ?></dd>

            <dt class="col-sm-4">Address</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['address'] ?? 'N/A'); ?></dd>

            <dt class="col-sm-4">Relationship</dt>
            <dd class="col-sm-8"><?php echo htmlspecialchars($cd['relationship'] ?? 'N/A'); ?></dd>
          </dl>
        </div>
        <?php
      }
      ?>

      <!-- Marriage Certificate Form -->
      <form class="marriage-form" id="marriageCertForm" method="POST" novalidate>
        <h3>Certificate #1</h3>

        <!-- Certificate Info -->
        <div class="mb-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Registry Number (Optional)</label>
              <input type="text" class="form-control" name="registry_id[]" placeholder="e.g., REG-2024-001" />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Number of Copies</label>
              <select class="form-select" name="copies[]" required>
                <option value="" selected disabled>Choose...</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Husband Info -->
        <div class="mb-4">
          <h3>Complete Name of the Husband</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="husband_firstname[]" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="husband_middlename[]" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="husband_lastname[]" required />
            </div>
          </div>
        </div>

        <!-- Wife Info -->
        <div class="mb-4">
          <h3>Complete Name of the Wife</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label required">First Name</label>
              <input type="text" class="form-control" name="wife_firstname[]" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="wife_middlename[]" />
            </div>
            <div class="col-md-4">
              <label class="form-label required">Last Name</label>
              <input type="text" class="form-control" name="wife_lastname[]" required />
            </div>
          </div>
        </div>

        <!-- Marriage Details -->
        <div class="mb-4">
          <h3>Marriage Details</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label required">Date of Marriage</label>
              <input type="date" class="form-control" name="marriagedate[]" required />
            </div>
            <div class="col-md-6">
              <label class="form-label required">Place of Marriage</label>
              <input type="text" class="form-control" name="marriageplace[]" placeholder="City, Municipality, Province" required />
            </div>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
          <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">
            <i class="fas fa-plus me-2"></i>Add Another Certificate
          </button>
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">
            <i class="fas fa-arrow-right me-2"></i>NEXT
          </button>
        </div>
      </form>
    </div>
  </main>

  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Add Another Certificate?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you still requesting for yourself?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="handleYes()">YES</button>
          <button type="button" class="btn btn-warning" onclick="handleNo()">NO (Different Requester)</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Checklist Modal -->
  <?php include "checklist_modal.php"; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- JavaScript Logic -->
  <script>
    let certCountYes = 1;

    function validateAndShowChecklist(btn) {
      const form = btn.closest('form');
      
      // Validate all required fields
      let isValid = true;
      const requiredFields = form.querySelectorAll('input[required], select[required]');
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          isValid = false;
        } else {
          field.classList.remove('is-invalid');
        }
      });
      
      if (!isValid) {
        alert('Please fill in all required fields.');
        return;
      }
      
      // Show checklist modal
      const checklistModal = new bootstrap.Modal(document.getElementById('checklistModal'));
      checklistModal.show();
      
      // Store current form for submission
      window.currentForm = form;
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
      const firstCert = allCerts[0];
      const lastCert = allCerts[allCerts.length - 1];

      const oldBtns = lastCert.querySelector(".d-flex.justify-content-between");
      if (oldBtns) oldBtns.remove();

      const newCert = lastCert.cloneNode(true);
      certCountYes++;

      const form = newCert.querySelector("form");
      form.className = "marriage-form";
      form.id = `marriageCertForm${certCountYes}`;
      form.querySelector("h3").textContent = `Certificate #${certCountYes}`;

      // Copy values from first certificate
      const firstInputs = firstCert.querySelectorAll("input, select");
      const newInputs = newCert.querySelectorAll("input, select");

      firstInputs.forEach((input, i) => {
        if (input.type !== "button" && input.type !== "submit") {
          newInputs[i].value = input.value;
        }
      });

      // Remove old remove button
      const oldRemoveBtn = newCert.querySelector(".btn-danger");
      if (oldRemoveBtn) oldRemoveBtn.remove();

      // Add action buttons
      const btnGroup = document.createElement("div");
      btnGroup.className = "d-flex justify-content-between mt-4";
      btnGroup.innerHTML = `
        <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">
          <i class="fas fa-plus me-2"></i>Add Another Certificate
        </button>
        <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">
          <i class="fas fa-arrow-right me-2"></i>NEXT
        </button>
      `;
      form.appendChild(btnGroup);

      // Add remove button
      const removeBtn = document.createElement("button");
      removeBtn.className = "btn btn-danger mt-3";
      removeBtn.type = "button";
      removeBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Remove Certificate';
      removeBtn.onclick = () => {
        if (confirm('Are you sure you want to remove this certificate?')) {
          newCert.remove();
          certCountYes--;
          restoreButtonsToLastCert();
        }
      };
      form.appendChild(removeBtn);

      container.appendChild(newCert);
    }

    function restoreButtonsToLastCert() {
      const allCerts = document.querySelectorAll(".form-box");
      if (allCerts.length === 0) return;

      const lastCert = allCerts[allCerts.length - 1];
      const form = lastCert.querySelector("form");

      if (!form.querySelector(".btn-add")) {
        const btnGroup = document.createElement("div");
        btnGroup.className = "d-flex justify-content-between mt-4";
        btnGroup.innerHTML = `
          <button type="button" class="btn btn-add px-4 py-2" onclick="showConfirmation()">
            <i class="fas fa-plus me-2"></i>Add Another Certificate
          </button>
          <button type="button" class="btn btn-next px-4 py-2" onclick="validateAndShowChecklist(this)">
            <i class="fas fa-arrow-right me-2"></i>NEXT
          </button>
        `;
        form.appendChild(btnGroup);
      }
    }

    function addDifferentRequester() {
      if (confirm('This will start a new request with different requester information. Continue?')) {
        window.location.href = 'marriage.php?different_requester=yes';
      }
    }

    // Prevent invalid form submission
    document.getElementById('marriageCertForm').addEventListener('submit', function(e) {
      const requiredFields = this.querySelectorAll('input[required], select[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          isValid = false;
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
      }
    });
  </script>
</body>
</html>