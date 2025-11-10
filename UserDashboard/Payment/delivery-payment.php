<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Document Delivery - Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body, html {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 30%, #3b82f6 60%, #60a5fa 80%, #f97316 100%);
            z-index: -2;
        }
        
        .background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(30, 58, 138, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
            z-index: -1;
        }
        
        /* Modern header styling */
        header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 15px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-img {
            height: 40px;
            margin-right: 12px;
            filter: brightness(1.2) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
        }

        .container {
            display: flex;
            min-height: 100vh;
            padding-top: 85px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .image-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .image-section img {
            max-width: 400px;
            height: auto;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.1));
        }

        .payment-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            margin: 20px;
            border-radius: 24px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 500px;
        }

        .payment-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .payment-title {
            font-size: 28px;
            font-weight: 700;
            margin-left: 12px;
            color: #000000;
        }

        .payment-divider {
            height: 2px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6, transparent);
            margin: 20px 0;
            border-radius: 2px;
        }

        .payment-info-section {
            margin: 20px 0;
            padding: 24px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 16px;
            border: 2px solid rgba(59, 130, 246, 0.2);
            text-align: center;
        }

        .payment-instructions {
            margin-bottom: 16px;
            text-align: center;
            font-size: 16px;
            color: #000000;
            line-height: 1.6;
        }

        .phone-number {
            color: #1e3a8a;
            font-weight: bold;
            font-size: 18px;
        }

        .payment-amount {
            color: #1e3a8a;
            font-size: 28px;
            font-weight: bold;
            margin: 16px 0;
            padding: 16px;
            background: rgba(30, 58, 138, 0.1);
            border-radius: 12px;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 16px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            position: relative;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-status {
            margin-top: 10px;
            font-size: 14px;
            color: #059669;
            font-weight: 600;
        }

        .submit-btn {
            background: linear-gradient(135deg, #1e3a8a 0%, #f97316 100%);
            color: white;
            border: none;
            padding: 18px 32px;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            margin-top: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(30, 58, 138, 0.5);
        }

        .submit-btn:active {
            transform: translateY(0px);
        }

        /* Modal styles */
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            max-width: 480px;
            width: 90%;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.15),
                0 8px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(30px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal h2 {
            color: #000000;
            margin-bottom: 16px;
            font-size: 24px;
            font-weight: 700;
        }

        .modal p {
            color: #000000;
            margin-bottom: 32px;
            font-size: 16px;
            line-height: 1.6;
        }

        .button {
            background: linear-gradient(135deg, #1e3a8a 0%, #f97316 100%);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(30, 58, 138, 0.5);
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .float-element {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .float-element:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .float-element:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .float-element:nth-child(3) {
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.8;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .image-section {
                min-height: 30vh;
                padding: 20px;
            }
            
            .payment-section {
                margin: 10px;
                padding: 24px;
                max-width: none;
            }

            .payment-title {
                font-size: 24px;
            }

            .image-section img {
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating background elements -->
    <div class="floating-elements">
        <div class="float-element"></div>
        <div class="float-element"></div>
        <div class="float-element"></div>
    </div>

    <div class="background"></div>
    
    <!-- Header -->
    <header>
        <img src="../../images/Logo 1.png" alt="Logo 1" class="logo-img" />
        <img src="../../images/Logo 2.png" alt="Logo 2" class="logo-img" />
    </header>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Left side - Image -->
        <div class="image-section">
            <img src="../../images/delivery.png" alt="Delivery Service">
        </div>
        
        <!-- Right side - Payment Form -->
        <div class="payment-section">
            <div class="payment-header">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 4H4C2.89 4 2.01 4.89 2.01 6L2 18C2 19.11 2.89 20 4 20H20C21.11 20 22 19.11 22 18V6C22 4.89 21.11 4 20 4ZM20 18H4V12H20V18ZM20 8H4V6H20V8Z" fill="url(#gradient1)"/>
                    <defs>
                        <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#1e3a8a"/>
                            <stop offset="100%" style="stop-color:#f97316"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="payment-title">Payment</div>
            </div>
            
            <div class="payment-divider"></div>
            
            <!-- Payment Information Section -->
            <div class="payment-info-section">
                <p class="payment-instructions">
                    Pay through this number: <span class="phone-number">09458745824</span>
                </p>
                <div class="payment-amount">₱ 100.00</div>
                <button class="upload-btn" onclick="triggerFileUpload()">
                    <input type="file" class="file-input" id="receiptUpload" accept="image/*,.pdf" onchange="handleFileUpload(this)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z" fill="currentColor"/>
                    </svg>
                    Upload Receipt
                </button>
                <div id="fileStatus" class="file-status"></div>
            </div>
            
            <button type="button" class="submit-btn" onclick="submitForm()">NEXT</button>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal-container" id="confirmationModal">
        <div class="modal">
            <h2>Payment Successful!</h2>
            <p>We will Email or Text you for updates regarding your document delivery.</p>
            <button class="button" onclick="redirectToHome()">OK</button>
        </div>
    </div>
    
    <script>
        let receiptUploaded = false;

        function triggerFileUpload() {
            document.getElementById('receiptUpload').click();
        }

        function handleFileUpload(input) {
            const fileStatus = document.getElementById('fileStatus');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
                
                fileStatus.innerHTML = `✓ File uploaded: ${fileName} (${fileSize} MB)`;
                fileStatus.style.color = '#059669';
                receiptUploaded = true;
            } else {
                fileStatus.innerHTML = '';
                receiptUploaded = false;
            }
        }

        function submitForm() {
            if (!receiptUploaded) {
                alert("Please upload your receipt before proceeding.");
                return;
            }
            
            // Redirect to delivery-form.php instead of showing modal
            window.location.href = 'delivery-form.php';
        }

        function redirectToHome() {
            document.getElementById('confirmationModal').style.display = 'none';
            window.location.href = 'index.html';
        }
    </script>
</body>
</html>