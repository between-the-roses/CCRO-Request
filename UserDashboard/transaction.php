<?php
// filepath: c:\xampp\htdocs\CCRO-Request\UserDashboard\transaction.php

function generateTransactionNumber($conn) {
    try {
        // Get the highest customer_id to determine next sequential number
        $stmt = $conn->prepare("SELECT MAX(customer_id) as max_id FROM customer");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nextNumber = ($result && $result['max_id']) ? $result['max_id'] + 1 : 1;
        
        // Format with leading zeros (CCR-01, CCR-02, etc.)
        $transactionNumber = 'CCR-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        
        return $transactionNumber;
        
    } catch (PDOException $e) {
        // Fallback: use current timestamp
        return 'CCR-' . date('His'); // Hours, minutes, seconds
    }
}

function generateTransactionFromId($customer_id) {
    // Generate transaction number directly from customer_id
    return 'CCR-' . str_pad($customer_id, 2, '0', STR_PAD_LEFT);
}

function generateRandomTransactionNumber() {
    // Generate random transaction number with timestamp
    $randomNumber = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
    return 'CCR-' . $randomNumber;
}

function generateTimestampBasedTransaction() {
    // Generate transaction number based on current time
    $timeNumber = date('His'); // HHMMSS format
    $shortNumber = substr($timeNumber, -2); // Last 2 digits
    return 'CCR-' . $shortNumber;
}

function generateSessionBasedTransaction() {
    // Generate transaction number using session ID
    $sessionId = session_id();
    $shortId = substr(md5($sessionId), 0, 2); // First 2 chars of MD5
    $numericId = str_pad(hexdec($shortId) % 100, 2, '0', STR_PAD_LEFT);
    return 'CCR-' . $numericId;
}

// Usage examples:
/*
// Method 1: Pre-generate before INSERT (recommended)
$transactionNumber = generateTransactionNumber($conn);

// Method 2: Generate after INSERT using customer_id
$customer_id = $conn->lastInsertId();
$transactionNumber = generateTransactionFromId($customer_id);

// Method 3: Random/timestamp based
$transactionNumber = generateRandomTransactionNumber();
*/
?>