<?php include 'includes/navbar.php'; ?>

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
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
    }
    .center-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .verification-alert {
        font-size: 1.1rem;
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 5px;
        padding: 1.5rem;
        text-align: center;
        max-width: 600px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>

  <div class="center-container">
    <div class="verification-alert">
      <strong>Request successful!</strong><br>
      Your request has been submitted and is now pending admin confirmation.<br><br>
      <em>Once your documents have been reviewed, updates will be sent to your registered Gmail account or mobile number.</em><br>
      Please keep your lines open and monitor your inbox or SMS for confirmation.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
