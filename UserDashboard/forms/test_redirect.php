<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\forms\test_redirect.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirect Test</title>
</head>
<body>
    <h1>Redirect Successful!</h1>
    <p>You have successfully reached the form page.</p>
    
    <?php if (isset($_SESSION['customer_data'])): ?>
        <h3>Customer Data:</h3>
        <pre><?php print_r($_SESSION['customer_data']); ?></pre>
    <?php else: ?>
        <p style="color: red;">No customer data found in session!</p>
        <a href="../customer.php">Go back to customer form</a>
    <?php endif; ?>
</body>
</html>