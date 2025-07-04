<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\forms\death.php
session_start();
include __DIR__ . "/../backend/db.php";

// // Check if customer data exists in session
// if (!isset($_SESSION['customer_data'])) {
//     header("Location: http://localhost/CCRO-Request/customer.php");
//     exit;
// }

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
                
                // Handle date fields - convert empty strings to null
                $deathdate = !empty($_POST['deathdate'][$i]) ? $_POST['deathdate'][$i] : null;
                $birthdate = !empty($_POST['birthdate'][$i]) ? $_POST['birthdate'][$i] : null;
                
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
            
            // Handle date fields - convert empty strings to null
            $deathdate = !empty($_POST['deathdate']) ? $_POST['deathdate'] : null;
            $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
            
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
        echo "<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        
        // Debug information
        echo "<div class='alert alert-info'>";
        echo "<strong>Debug Information:</strong><br>";
        echo "Certificate Type: " . htmlspecialchars($certificate_type) . "<br>";
        if (isset($_POST['deathdate'])) {
            echo "Death Date: '" . htmlspecialchars($_POST['deathdate']) . "'<br>";
        }
        if (isset($_POST['birthdate'])) {
            echo "Birth Date: '" . htmlspecialchars($_POST['birthdate']) . "'<br>";
        }
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Certificate Request Form - Print View</title>
    
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        /* Print specific styles */
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
        }

        /* A4 Page container */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Header section */
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 60px;
            height: 60px;
        }

        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 60px;
            height: 60px;
        }

        .header-text {
            margin: 0 80px;
        }

        .header-text h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .header-text h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 3px 0;
        }

        .header-text p {
            font-size: 11px;
            margin: 2px 0;
        }

        .form-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            text-decoration: underline;
        }

        /* Checkbox options section */
        .checkbox-section {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .checkbox-options {
            flex: 1;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin: 8px 0;
            font-size: 11px;
        }

        .checkbox {
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 8px;
            display: inline-block;
        }

        .date-section {
            text-align: right;
            min-width: 200px;
        }

        .date-input {
            border: none;
            border-bottom: 1px solid #000;
            width: 150px;
            padding: 2px;
            margin-left: 10px;
        }

        /* Form sections */
        .form-section {
            margin: 20px 0;
            border: 2px dashed #000;
            padding: 15px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: black;
        }

        .section-subtitle {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            color: black;
        }

        /* Form fields */
        .form-row {
            display: flex;
            margin: 10px 0;
            align-items: center;
        }

        .form-group {
            flex: 1;
            margin-right: 15px;
        }

        .form-group:last-child {
            margin-right: 0;
        }

        .form-label {
            font-weight: bold;
            color:black;
            margin-bottom: 3px;
            display: block;
            font-size: 11px;
        }

        .required:before {
            content: "*";
            color: #d32f2f;
            margin-right: 2px;
        }

        .form-input {
            border: none;
            border-bottom: 1px solid #000;
            width: 100%;
            padding: 2px 0;
            font-size: 11px;
        }

        .form-input-short {
            width: 80px;
        }

        .placeholder-text {
            font-size: 9px;
            color: #666;
            font-style: italic;
            text-align: center;
            margin-top: 2px;
        }

        /* Gender checkboxes */
        .gender-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .gender-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Certification section */
        .certification-section {
            border: 2px dashed #000;
            padding: 15px;
            margin: 20px 0;
        }

        /* Note section */
        .note-section {
            text-align: center;
            margin: 20px 0;
            font-size: 11px;
        }

        .note-text {
            font-weight: bold;
        }

        /* Print button */
        .print-controls {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
        }

        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 10px;
        }

        .print-btn:hover {
            background: #0056b3;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .print-container {
                width: 100%;
                margin: 10px;
                padding: 15px;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .form-group {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls (hidden when printing) -->
    <div class="print-controls no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Print Form</button>
        <button class="print-btn" onclick="window.history.back()" style="background: #6c757d;">← Back to Form</button>
    </div>

    <div class="print-container">
        <!-- Header Section -->
        <div class="header-section">
            <img class="logo-left" src="../images/Logo 2.png" alt="Iligan Logo">
            <img class="logo-right" src="../images/Logo 1.png" alt="Philippines Logo">
            
            <div class="header-text">
                <h1>Republic of the Philippines</h1>
                <p><em>City of Iligan</em></p>
                <h2>CITY CIVIL REGISTRAR'S OFFICE</h2>
                <p>Ground Flr, Pedro Generalao Bldg. Buhanginan Hill, Palao, Iligan City</p>
                <div class="form-title">DECEASED REQUEST FORM</div>
            </div>
        </div>

        <!-- Checkbox Options and Date Section -->
        <div class="checkbox-section">
            <div class="checkbox-options">
                <div class="checkbox-item">
                    <span class="checkbox"></span>
                    <span>Certified Photocopy</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox"></span>
                    <span>Civil Registry Form No. 2A (Death Available)</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox"></span>
                    <span>Civil Registry Form No. 2B (Death - Not Available)</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox"></span>
                    <span>Civil Registry Form No. 2C (Death Destroyed)</span>
                </div>
            </div>
            
            <div class="date-section">
                <strong>Date:</strong>
                <input type="text" class="date-input" value="<?php echo date('m/d/Y'); ?>">
            </div>
        </div>

        <!-- Client Information Section -->
        <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 12px;">
            TO BE FILLED OUT BY CLIENT
        </div>

        <!-- Death Certificate Details -->
        <div class="form-section">
            <div class="section-title">DEATH CERTIFICATE DETAILS</div>

            <!-- Complete Name -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Complete Name:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(First Name) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Middle Name) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Last Name)</div>
                </div>
            </div>

            <!-- Date Information Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Date of Death:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(MM/DD/YYYY)</div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Date of Birth:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(MM/DD/YYYY)</div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Age:</label>
                    <input type="text" class="form-input form-input-short">
                </div>
            </div>

            <!-- Sex Selection -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Sex:</label>
                    <div class="gender-section">
                        <div class="gender-option">
                            <span class="checkbox"></span>
                            <span>Male</span>
                        </div>
                        <div class="gender-option">
                            <span class="checkbox"></span>
                            <span>Female</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Religion:</label>
                    <input type="text" class="form-input">
                </div>
            </div>

            <!-- Personal Information Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Citizenship:</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label required">Residence:</label>
                    <input type="text" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label required">Occupation:</label>
                    <input type="text" class="form-input">
                </div>
            </div>

            <!-- Parents Information -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Name of Father:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(Full Name)</div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Name of Mother:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(Full Name)</div>
                </div>
            </div>

            <!-- Burial Information -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Corpse Disposal (Burial/Cremation):</label>
                    <input type="text" class="form-input">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label required">Name and Address of Cemetery or Crematory:</label>
                    <input type="text" class="form-input">
                </div>
            </div>
        </div>

        <!-- Certification of Informant -->
        <div class="certification-section">
            <div class="section-subtitle">CERTIFICATION OF INFORMANT</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(Full Name)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Relation to the Deceased:</label>
                    <input type="text" class="form-input">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Address:</label>
                    <input type="text" class="form-input">
                    <div class="placeholder-text">(Full Address)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Cell Number:</label>
                    <input type="text" class="form-input">
                </div>
            </div>
        </div>

        <!-- Note Section -->
        <div class="note-section">
            <div class="note-text">
                NOTE: "DO NOT LEAVE ANY BLANKS. ALL INFORMATION MUST BE BE FILLED OUT."
            </div>
        </div>
    </div>

    <script>
        // Auto-print functionality
        function autoPrint() {
            if (window.location.search.includes('print=1')) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        }

        // Call auto-print on page load
        document.addEventListener('DOMContentLoaded', autoPrint);

        // Print function
        function printForm() {
            window.print();
        }

        // Handle print dialog close
        window.addEventListener('afterprint', function() {
            // Optional: redirect back or show success message
            console.log('Print dialog closed');
        });
    </script>
</body>
</html>