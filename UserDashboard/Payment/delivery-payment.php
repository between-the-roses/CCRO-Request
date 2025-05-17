<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Iligan City Document Delivery - Payment</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
    }

    header {
      background-color: white;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      padding: 20px 30px;
      box-shadow: 0 8px 11px rgba(14, 55, 54, 0.15);
      display: flex;
      align-items: center;
    }

    .logo-img {
      height: 45px;
      margin-right: 15px;
    }

    .content-container {
      display: flex;
      width: 100%;
      height: 100%;
      margin-top: 100px;
    }

    .image-container {
      flex: 1;
      background-color: #59b6e4;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .payment-container {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      max-height: 100vh;
      overflow-y: auto;
    }

    .payment-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .payment-icon {
      margin-right: 10px;
    }

    h2 {
      margin: 0;
      font-size: 1.5rem;
    }

    .qr-container {
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 5px;
      margin: 15px 0;
      text-align: center;
      background-color: white;
    }

    .qr-code {
      max-width: 180px;
      height: auto;
      margin: 0 auto;
      display: block;
      border: 1px solid #eee;
      padding: 5px;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .payment-instruction {
      text-align: center;
      margin: 10px 0;
      font-size: 14px;
    }

    .payment-number {
      font-weight: bold;
      color: #3498db;
      text-decoration: underline;
    }

    .payment-amount {
      color: #003366;
      font-size: 22px;
      font-weight: bold;
      margin: 8px 0;
    }

    .custom-upload-button {
      display: inline-flex;
      align-items: center;
      padding: 8px 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
      cursor: pointer;
      background-color: #fff;
      font-size: 14px;
      color: #000;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
      margin: 8px auto;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    .custom-upload-button:hover {
      background-color: #f1f1f1;
    }

    .custom-upload-button img {
      width: 16px;
      height: 16px;
      margin-right: 5px;
    }

    .button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s;
      font-size: 16px;
      width: 100%;
      margin-top: 15px;
    }

    .next-button {
      background-color: #3498db;
      color: white;
      max-width: 200px;
      margin: 15px auto 0;
    }

    .modal-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 10;
    }

    .modal {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .confirmation-button {
      background-color: #4CAF50;
      color: white;
      width: 100%;
      padding: 12px;
      font-size: 16px;
    }

    .delivery-image {
      max-width: 100%;
      height: auto;
    }
  </style>
</head>
<body>
  <header>
    <img src="../../images/Logo 1.png" alt="Logo 1" class="logo-img" />
    <img src="../../images/Logo 2.png" alt="Logo 2" class="logo-img" />
  </header>

  <div class="content-container">
    <div class="image-container">
      <div class="delivery-image-wrapper">
        <img src="../../images/delivery.png" alt="Delivery Service" class="delivery-image">
      </div>
    </div>

    <div class="payment-container">
      <div class="payment-header">
        <span class="payment-icon">💳</span>
        <h2>Payment</h2>
      </div>

      <div class="qr-container">
        <img src="../../images/QRcode.png" alt="QR Code" class="qr-code">
        <p style="color: #666; font-size: 12px; margin-top: 8px;">Transfer fees may apply.</p>
        <p style="color: #0066cc; font-weight: bold; font-size: 16px;">JI****N RO*E B.</p>
        <p style="color: #999; font-size: 12px;">Mobile No.: 093• ••••032</p>
        <p style="color: #999; font-size: 12px;">User ID: •••••••••••WGPSUH</p>
        <div class="payment-amount">₱ 100.00</div>
      </div>

      <div class="payment-instruction">
        Pay by Scanning this QR CODE<br>or<br>
        Pay through this number: <span class="payment-number">09458745824</span>
      </div>

      <label for="receipt-upload" class="custom-upload-button">
        <img src="data:image/svg+xml;base64,PHN2ZyBmaWxsPSIjMDAwMDAwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTkgMTFoLTYuNTk3bDIuNTM2LTIuNTM2LTEuNDE0LTEuNDE0TDExIDEyLjU4NiA2LjQ3NSA3LjA1OCA1LjA2MSA4LjQ3M2wyLjUzNiAyLjUzNkgyVjE5aDE3di0ySDIwVjExeiIvPjwvc3ZnPg==" alt="Upload Icon">
        Upload Receipt
        <input type="file" id="receipt-upload" accept="image/*" style="display: none;">
      </label>

      <div style="text-align: center;">
        <button class="button next-button" onclick="showConfirmationModal()">NEXT</button>
      </div>
    </div>
  </div>

  <!-- Confirmation Modal -->
  <div class="modal-container" id="confirmationModal">
    <div class="modal">
      <h2>Payment Successful!</h2>
      <p>We will Email or Text you for updates regarding your document delivery.</p>
      <button class="button confirmation-button" onclick="redirectToHome()">OK</button>
    </div>
  </div>

  <script>
    let receiptUploaded = false;

    document.getElementById('receipt-upload').addEventListener('change', function () {
      if (this.files.length > 0) {
        alert('Receipt uploaded successfully!');
        receiptUploaded = true;
      }
    });

    function showConfirmationModal() {
      if (!receiptUploaded) {
        alert("Please upload your receipt before proceeding.");
        return;
      }
      document.getElementById('confirmationModal').style.display = 'flex';
    }

    function redirectToHome() {
      document.getElementById('confirmationModal').style.display = 'none';
      window.location.href = 'index.html';
    }

    function adjustQRCode() {
      // Optional: Image load adjustments
    }

    window.addEventListener('load', function () {
      const qrCode = document.querySelector('.qr-code');
      if (qrCode.complete) {
        adjustQRCode();
      } else {
        qrCode.addEventListener('load', adjustQRCode);
      }
    });
  </script>
</body>
</html>
