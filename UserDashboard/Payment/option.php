<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Document Delivery</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        .background {
            background-image: url('../../images/background.png');
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            filter: blur(0px);
            margin-top: 85px; /* Added to make room for the fixed header */
        }
        
        /* Updated header styling to match paste-2.txt */
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
        
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
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
        
        .option-modal {
            display: none;
        }
        
        .button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .next-button {
            background-color: #4CAF50;
            color: white;
        }
        
        .next-button:hover {
            background-color: #45a049;
        }
        
        .pickup-button {
            background-color: #f8f9fa;
            color: #212529;
            display: flex;
            align-items: center;
        }
        
        .pickup-button:hover {
            background-color: #e2e6ea;
        }
        
        .delivery-button {
            background-color: #f8f9fa;
            color: #212529;
            display: flex;
            align-items: center;
        }
        
        .delivery-button:hover {
            background-color: #e2e6ea;
        }
        
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <!-- New header section matching paste-2.txt -->
    <header>
        <img src="../../images/Logo 1.png" alt="Logo 1" class="logo-img" />
        <img src="../../images/Logo 2.png" alt="Logo 2" class="logo-img" />
    </header>
    
    <div class="background"></div>
    
    <!-- First Modal - Document Found Message -->
    <div class="modal-container" id="documentFoundModal">delivery-payment<div class="modal">
            <h2>Document Found!</h2>
            <p>Your requested document has been found and is ready for processing. Please click Next to choose your preferred delivery method.</p>
            <button class="button next-button" onclick="showOptionsModal()">Next</button>
        </div>
    </div>
    
    <!-- Second Modal - Delivery Options -->
    <div class="modal-container option-modal" id="optionsModal">
        <div class="modal">
            <h2>Select your preferred option?</h2>
            <div style="display: flex; justify-content: center;">
                <button class="button pickup-button" onclick="selectOption('pickup')">
                    <span class="icon">🔄</span> Pick - Up
                </button>
                <button class="button delivery-button" onclick="selectOption('delivery')">
                    <span class="icon">🚚</span> Delivery
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function showOptionsModal() {
            document.getElementById('documentFoundModal').style.display = 'none';
            document.getElementById('optionsModal').style.display = 'flex';
        }
        
        function selectOption(option) {
            if (option === 'pickup') {
                // Redirect to the pickup form page
                window.location.href = 'pickup-form.html';
            } else if (option === 'delivery') {
                // Redirect to the delivery form page
                window.location.href = 'delivery-form.html';
            }
        }
    </script>
</body>
</html>