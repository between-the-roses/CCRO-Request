<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iligan City Document Delivery</title>
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
        
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
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
            font-size: 28px;
            font-weight: 700;
        }
        
        .modal p {
            color: #4a5568;
            margin-bottom: 32px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .option-modal {
            display: flex;
        }
        
        .button {
            padding: 16px 32px;
            margin: 8px;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .button:hover::before {
            left: 100%;
        }
        
        .next-button {
            background: linear-gradient(135deg, #1e3a8a 0%, #f97316 100%);
            color: white;
            min-width: 140px;
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
        }
        
        .next-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(30, 58, 138, 0.5);
        }
        
        .next-button:active {
            transform: translateY(0px);
        }
        
        .options-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 8px;
        }
        
        .pickup-button, .delivery-button {
            background: rgba(255, 255, 255, 0.8);
            color: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 32px;
            border: 2px solid rgba(30, 58, 138, 0.2);
            min-width: 200px;
            font-size: 18px;
        }
        
        .pickup-button:hover, .delivery-button:hover {
            background: rgba(30, 58, 138, 0.1);
            border-color: rgba(30, 58, 138, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.2);
        }
        
        .icon {
            margin-right: 12px;
            font-size: 24px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #f97316 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
        
        .pickup-icon::before {
            content: '📍';
            font-size: 16px;
        }
        
        .delivery-icon::before {
            content: '🚚';
            font-size: 16px;
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
        
        @media (max-width: 768px) {
            .modal {
                padding: 32px 24px;
                margin: 20px;
            }
            
            .modal h2 {
                font-size: 24px;
            }
            
            .options-container {
                flex-direction: column;
            }
            
            .pickup-button, .delivery-button {
                min-width: unset;
                width: 100%;
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

    <?php include '/../includes/navbar.php'; ?>
    
    <div class="background"></div>
    
    <!-- Delivery Options Modal -->
    <div class="modal-container" id="optionsModal">
        <div class="modal">
            <h2>Select your preferred option</h2>
            <div class="options-container">
                <button class="button pickup-button" onclick="selectOption('pickup')">
                    <span class="icon pickup-icon"></span>
                    Pick-Up
                </button>
                <button class="button delivery-button" onclick="selectOption('delivery')">
                    <span class="icon delivery-icon"></span>
                    Delivery
                </button>
            </div>
        </div>
    </div>
    
    <script>
        function selectOption(option) {
            if (option === 'pickup') {
                window.location.href = '/CCRO-Request/UserDashboard/payment/pickup-form.php';
            } else if (option === 'delivery') {
                window.location.href = './delivery-payment.php'; }
        }
        
        // Add smooth interaction effects
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.button');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0px)';
                });
            });
        });
    </script>
</body>
</html>