<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification</title>
  <style>
    body {
        min-height: 100vh;
        margin: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 70px; /* Add padding instead of margin on container */
    }
    
    .center-container {
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: transparent; /* Remove white overlay to show gradient */
    }
    
    .verification-alert {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
        max-width: 650px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px); /* Add subtle blur effect */
    }
    
    .verification-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #28a745, #20c997, #17a2b8);
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem auto;
        animation: bounce 2s infinite;
    }
    
    .success-icon i {
        font-size: 2.5rem;
        color: white;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    .success-title {
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .transaction-box {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border: 2px dashed #28a745;
        border-radius: 15px;
        padding: 1.5rem;
        margin: 2rem 0;
        position: relative;
    }
    
    .transaction-label {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }
    
    .transaction-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #28a745;
        font-family: 'Courier New', monospace;
        letter-spacing: 3px;
    }
    
    .copy-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: all 0.3s ease;
    }
    
    .copy-btn:hover {
        background: #1e7e34;
        transform: translateY(-2px);
    }
    
    .description-text {
        font-size: 1.1rem;
        color: #495057;
        line-height: 1.6;
        margin-bottom: 2rem;
    }
    
    .contact-info {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        font-style: italic;
        color: #6c757d;
    }
    
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-custom {
        padding: 12px 24px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,123,255,0.3);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary-custom {
        background: #f8f9fa;
        color: #495057;
        border: 2px solid #dee2e6;
    }
    
    .btn-secondary-custom:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        text-decoration: none;
        color: #495057;
    }
  </style>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <?php include 'includes/navbar.php'; ?>

  <div class="center-container">
    <div class="verification-alert">
      <div class="success-icon">
        <i class="fas fa-check"></i>
      </div>
      
      <h1 class="success-title">Request Submitted Successfully!</h1>
      
      <?php if (isset($_SESSION['transaction_number'])): ?>
        <div class="transaction-box">
          <button class="copy-btn" onclick="copyTransaction()" title="Copy to clipboard">
            <i class="fas fa-copy"></i> Copy
          </button>
          <div class="transaction-label">Transaction Number</div>
          <div class="transaction-number" id="transactionNumber">
            <?php echo htmlspecialchars($_SESSION['transaction_number']); ?>
          </div>
        </div>
      <?php endif; ?>
      
      <p class="description-text">
        Your request has been submitted and is now pending admin confirmation.
        <br><strong>Please save your transaction number for tracking purposes.</strong>
      </p>
      
      <div class="contact-info">
        <i class="fas fa-info-circle me-2"></i>
        Once your documents have been reviewed, updates will be sent to your registered email account or mobile number.
        Please keep your lines open and monitor your inbox or SMS for confirmation.
      </div>
      
      <div class="action-buttons">
        <a href="../index.php" class="btn-custom btn-primary-custom">
          <i class="fas fa-home me-2"></i>Back to Home
        </a>
        <a href="tracker.php" class="btn-custom btn-secondary-custom">
          <i class="fas fa-search me-2"></i>Track Status
        </a>
      </div>
    </div>
  </div>

  <script>
    function copyTransaction() {
      const transactionNumber = document.getElementById('transactionNumber').textContent.trim();
      navigator.clipboard.writeText(transactionNumber).then(function() {
        const btn = document.querySelector('.copy-btn');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.style.background = '#28a745';
        
        setTimeout(function() {
          btn.innerHTML = originalContent;
          btn.style.background = '#28a745';
        }, 2000);
      }).catch(function(err) {
        console.error('Could not copy text: ', err);
      });
    }
    
    // Clear session transaction number after display (optional)
    <?php if (isset($_SESSION['transaction_number'])): ?>
      // Auto-clear transaction from session after 5 minutes
      setTimeout(function() {
        fetch('clear-transaction.php', {method: 'POST'});
      }, 300000);
    <?php endif; ?>
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
