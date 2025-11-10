<?php

// ✅ FIXED: Correct path to autoloader
require __DIR__ . '/vendor/autoload.php';

// ✅ FIXED: Add proper namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      // Disable debug output
    $mail->isSMTP();                                         // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';     // Set the SMTP server
    $mail->SMTPAuth   = true;                          // Enable SMTP authentication
    $mail->Username   = 'gptchater4@gmail.com';           // SMTP username
    $mail->Password   = 'fluy ouvs qcvy gwjc';           // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable implicit TLS encryption
    $mail->Port       = 587;           // TCP port to connect to

    // Recipients
    $mail->setFrom('gptchater4@gmail.com', 'CeRS-noreply');
    $mail->addAddress('danielanthony.labajo@g.msuiit.edu.ph', 'Recipient Name');

    // Content
    $mail->isHTML(true);                      // Set email format to HTML
    $mail->Subject = "CCRO-CeRS: Document Request Found (Transaction No. {$transaction_no})";

$mail->Body = '
    <p><b>YOUR REQUEST DOCUMENT HAS BEEN FOUND</b></p>

    <p>Hi <b>' . htmlspecialchars($fullname) . '</b>,</p>

    <p>Good news! The document you requested through the 
    <b>CCRO-CeRS (Civil Registry Certificate Request System)</b> has been <b>successfully located</b> in our Civil Registry records. Below are your transaction details:</p>

    <hr>
    <p><b>📄 Transaction Details</b><br>
    Transaction No.: <b>' . htmlspecialchars($transaction_no) . '</b><br>
    Document Requested: <b>' . htmlspecialchars($certificate_type) . ' Certificate</b><br>
    Date Requested: <b>' . htmlspecialchars($date_submitted) . '</b><br>
    Copies Requested: <b>' . htmlspecialchars($copies) . '</b><br>
    Purpose: <b>' . htmlspecialchars($purpose) . '</b></p>
    <hr>

    <p><b>💳 Payment Reminder</b><br>
    To proceed with the preparation and release of your document, please settle your payment of <b>₱100.00 per copy</b> through one of the following options:</p>

    <p><u>Payment Options:</u><br>
    <b>1️⃣ Cash Payment:</b> Pay at the <b>Cashier Section</b> of the City Civil Registry Office, Iligan City during office hours.<br><br>
    <b>2️⃣ GCash Payment:</b><br>
    GCash Number: <b>09XX-XXX-XXXX</b><br>
    Account Name: <b>City Civil Registry Office – Iligan</b><br>
    Amount: <b>₱100.00 per document</b><br>
    Reference: Include your <b>Transaction No. (' . htmlspecialchars($transaction_no) . ')</b> in the payment note.</p>

    <p>After payment, kindly <b>upload or present your proof of payment</b> through your CCRO-CeRS dashboard for verification.</p>

    <hr>
    <p><b>⚠️ Important Reminders:</b></p>
    <ul>
        <li>Processing will only begin once payment is verified.</li>
        <li>Make sure your submitted information matches the record found.</li>
        <li>Bring one valid ID when claiming the document (if pickup is required).</li>
        <li>Keep your <b>Transaction Number</b> for tracking and inquiries.</li>
        <li>Failure to pay within <b>5 working days</b> may result in automatic cancellation.</li>
    </ul>

    <p>Thank you for using the <b>CCRO-CeRS Online Request System</b>. Your cooperation helps us serve you better.</p>

    <p>Warm regards,<br>
    <b>City Civil Registry Office – Iligan City</b><br>
    📞 (063) 223-XXXX<br>
    ✉️ ccro.support@iligan.gov.ph<br>
    🌐 <a href="https://ccro.iligan.gov.ph">https://ccro.iligan.gov.ph</a></p>
';

$mail->AltBody = 'YOUR REQUEST DOCUMENT HAS BEEN FOUND

Hi ' . $fullname . ',
Good news! Your requested ' . $certificate_type . ' certificate has been found.

Transaction No.: ' . $transaction_no . '
Date Requested: ' . $date_submitted . '
Copies: ' . $copies . '
Purpose: ' . $purpose . '

Please pay ₱100.00 per copy at the CCRO Cashier Section or via GCash (09XX-XXX-XXXX, CCRO Iligan). 
Include your Transaction No. in the payment note.

Processing starts only after payment verification. 
Keep your Transaction No. for tracking and follow-ups.';


    $mail->send();
    echo '✅ Message has been sent';
    
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}