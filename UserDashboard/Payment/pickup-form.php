<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Document Pickup</title>
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

        .container {
            display: flex;
            height: 100vh;
            padding-top: 85px; /* Leave space for the fixed header */
        }

        .image-section {
            flex: 1;
            background-color: #5ec2eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-section {
            flex: 1;
            background-color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        .payment-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .payment-title {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
        }

        .payment-options {
            margin-bottom: 20px;
        }

        .payment-divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 20px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .qr-section {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .qr-code {
            max-width: 200px;
            margin: 0 auto;
        }

        .qr-instructions {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }

        .phone-number {
            color: #0066cc;
            font-weight: bold;
        }

        .submit-btn {
            background-color: #4285f4;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: auto;
        }

        .submit-btn:hover {
            background-color: #3367d6;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border: 1px solid #ddd;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        /* Modal styles */
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
            z-index: 1100;
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

        .button {
            padding: 10px 20px;
            margin: 10px auto;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            background-color: #4285f4;
            color: white;
            display: block;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .image-section {
                height: 40vh;
            }
            
            .payment-section {
                height: 60vh;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <img src="../..//Logo 1.png" alt="Logo 1" class="logo-img" />
        <img src="../../images/Logo 2.png" alt="Logo 2" class="logo-img" />
    </header>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Left side - Images -->
        <div class="image-section">
            <img src="../../images/pickup above.png" alt="Payment Options" style="max-width: 30%; margin-bottom: 20px;">
            <div style="color: #333; font-size: 28px; font-weight: bold; margin: 10px 0;"></div>
            <img src="../../images/pickup low.png" alt="Pickup Illustration" style="max-width: 60%; margin-top: 10px;">
        </div>
        
        <!-- Right side - Payment Form -->
        <div class="payment-section">
            <div class="payment-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 4H4C2.89 4 2.01 4.89 2.01 6L2 18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4ZM20 18H4V12H20V18ZM20 8H4V6H20V8Z" fill="black"/>
                </svg>
                <div class="payment-title">Payment</div>
            </div>
            
            <div class="payment-options">
                <label style="margin-right: 20px;">
                    <input type="radio" name="payment" value="cash" checked onchange="togglePaymentMethod()"> Cash
                </label>
                <label>
                    <input type="radio" name="payment" value="gcash" onchange="togglePaymentMethod()"> Gcash
                </label>
            </div>
            
            <div class="payment-divider"></div>
            
            <!-- QR Code Section (initially hidden) -->
            <div class="qr-section" id="qrSection">
                <img src="../../images/QRcode.png" alt="Gcash QR Code" class="qr-code">
                <p class="qr-instructions">
                    Pay by Scanning this QR CODE<br>or<br>
                    Pay through this number: <span class="phone-number">09454874824</span>
                </p>
                <button class="upload-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z" fill="currentColor"/>
                    </svg>
                    Upload Receipt
                </button>
            </div>
            
            <!-- Form Fields -->
            <form id="pickupForm">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <label for="mobile">Mobile Number:</label>
                    <input type="tel" id="mobile" name="mobile" placeholder="Mobile Number" required>
                </div>
                
                <button type="button" class="submit-btn" onclick="submitForm()">SUBMIT</button>
            </form>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal-container" id="confirmationModal">
        <div class="modal">
            <h2 id="modalTitle">Submission Successful</h2>
            <p id="modalMessage"></p>
            <button class="button" onclick="closeModal()">OK</button>
        </div>
    </div>
    
    <script>
        function togglePaymentMethod() {
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const qrSection = document.getElementById('qrSection');
            
            if (paymentMethod === 'gcash') {
                qrSection.style.display = 'block';
            } else {
                qrSection.style.display = 'none';
            }
        }
        
        function submitForm() {
            const email = document.getElementById('email').value;
            const mobile = document.getElementById('mobile').value;
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            
            if (!email || !mobile) {
                alert("Please fill in all required fields.");
                return;
            }
            
            // Display confirmation modal with appropriate message
            const modal = document.getElementById('confirmationModal');
            const modalMessage = document.getElementById('modalMessage');
            
            if (paymentMethod === 'cash') {
                modalMessage.textContent = "We will email or text you when the document is ready to pick up. Don't forget to pay at the cashier before picking up the document.";
            } else {
                modalMessage.textContent = "We will email or text you when the document is ready to pick up.";
            }
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
            // Reset form
            document.getElementById('pickupForm').reset();
            document.getElementById('qrSection').style.display = 'none';
            document.querySelector('input[value="cash"]').checked = true;
        }
        
        // Initialize the form
        togglePaymentMethod();
    </script>
</body>
</html>