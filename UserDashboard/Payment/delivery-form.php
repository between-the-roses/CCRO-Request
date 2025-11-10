<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Document Delivery - Delivery Form</title>
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
            max-width: 1400px; /* Increased max-width for wider layout */
            margin: 0 auto;
            gap: 20px; /* Add gap between sections */
        }

        .image-section {
            flex: 0 0 45%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .image-section img {
            max-width: 100%;
            width: 400px;
            height: auto;
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.1));
        }

        .form-section {
            flex: 0 0 50%;
            padding: 30px;
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
        }

        .form-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .form-title {
            font-size: 26px;
            font-weight: 700;
            margin-left: 12px;
            color: #000000;
        }

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6, transparent);
            margin: 15px 0;
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 18px;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        /* Two-column form layout for wider container */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }

        .form-group.full-width {
            flex: 1 1 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #000000;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid rgba(30, 58, 138, 0.2);
            border-radius: 12px;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus {
            border-color: #1e3a8a;
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .note {
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
            padding: 14px;
            background: rgba(249, 115, 22, 0.1);
            border-radius: 12px;
            border-left: 4px solid #f97316;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            padding: 14px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 12px;
            border: 2px solid rgba(59, 130, 246, 0.2);
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 12px;
            margin-top: 2px;
            width: 18px;
            height: 18px;
            accent-color: #1e3a8a;
        }

        .checkbox-container label {
            color: #000000;
            font-weight: 500;
            font-size: 13px;
            line-height: 1.5;
            cursor: pointer;
        }

        .button-row {
            display: flex;
            gap: 16px;
            margin-top: auto;
        }

        .back-button {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            border: 2px solid rgba(100, 116, 139, 0.3);
            padding: 16px 28px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
        }

        .back-button:hover {
            background: rgba(100, 116, 139, 0.2);
            transform: translateY(-2px);
        }

        .submit-btn {
            background: linear-gradient(135deg, #1e3a8a 0%, #f97316 100%);
            color: white;
            border: none;
            padding: 16px 28px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
            position: relative;
            overflow: hidden;
            flex: 2;
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
        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
            }
        }

        @media (max-width: 968px) {
            .container {
                flex-direction: column;
                gap: 0;
            }
            
            .image-section {
                flex: none;
                min-height: 30vh;
                padding: 20px;
            }
            
            .form-section {
                flex: none;
                margin: 10px;
                padding: 24px;
            }

            .form-title {
                font-size: 24px;
            }

            .image-section img {
                width: 300px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .button-row {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding-top: 75px;
            }
            
            .form-section {
                margin: 5px;
                padding: 20px;
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
        
        <!-- Right side - Form -->
        <div class="form-section">
            <div class="form-header">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 13h8V3H9v6H3v4zm0 8h6v-2l-2-2H3v4zm10 0h8v-4h-4.5l-1.5 1.5V21zm7.5-18L18 6.5V3h-4v8h8V9.5L20.5 8l1.5-1.5z" fill="url(#gradient2)"/>
                    <defs>
                        <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#1e3a8a"/>
                            <stop offset="100%" style="stop-color:#f97316"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="form-title">Delivery</div>
            </div>
            
            <div class="section-divider"></div>
            
            <div class="section-subtitle">Delivery Details</div>
            
            <form id="deliveryForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullName">Full Name of the Receiver:</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contactNumber">Contact Number:</label>
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter contact number" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Enter complete address" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" placeholder="Enter email address" required>
                    </div>
                </div>
                
                <div class="note">
                    <strong>Important:</strong> Confirmed transactions will not be refunded. Please make sure that the mobile number and amount are correct.
                </div>
                
                <div class="checkbox-container">
                    <input type="checkbox" id="confirm" name="confirm" required>
                    <label for="confirm">I confirm that all the details provided are correct and accurate.</label>
                </div>
                
                <div class="button-row">
                    <button type="button" class="back-button" onclick="goBack()">BACK</button>
                    <button type="submit" class="submit-btn">SUBMIT</button>
                </div>
            </form>
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
        document.getElementById('deliveryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form data
            const fullName = document.getElementById('fullName').value;
            const address = document.getElementById('address').value;
            const contactNumber = document.getElementById('contactNumber').value;
            const email = document.getElementById('email').value;
            const confirmed = document.getElementById('confirm').checked;
            
            // Validate form
            if (!fullName || !address || !contactNumber || !email || !confirmed) {
                alert('Please fill in all required fields and confirm the details.');
                return;
            }
            
            // Show the confirmation modal instead of redirecting
            document.getElementById('confirmationModal').style.display = 'flex';
        });
        
        function goBack() {
            window.history.back();
        }
        
        function redirectToHome() {
            document.getElementById('confirmationModal').style.display = 'none';
            window.location.href = 'index.html';
        }
    </script>
</body>
</html>