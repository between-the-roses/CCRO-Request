<?php

function generateTransactionNumber($conn) {
    try {
        // Get the highest transaction_id to determine next sequential number
        $stmt = $conn->prepare("SELECT MAX(transaction_id) as max_id FROM transaction");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nextNumber = ($result && $result['max_id']) ? $result['max_id'] + 1 : 1;
        
        // Format with leading zeros (CCR-01, CCR-02, etc.)
        $transactionNumber = 'CCR-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        
        return $transactionNumber;
        
    } catch (PDOException $e) {
        error_log("Error generating transaction number: " . $e->getMessage());
        // Fallback: use current timestamp
        return 'CCR-' . date('His'); // Hours, minutes, seconds
    }
}

function generateTransactionFromId($transaction_id) {
    try {
        // Validate transaction_id
        if (empty($transaction_id) || !is_numeric($transaction_id)) {
            throw new Exception("Invalid transaction_id provided");
        }
        
        // Generate transaction number directly from transaction_id
        $transactionNumber = 'CCR-' . str_pad($transaction_id, 2, '0', STR_PAD_LEFT);
        
        return $transactionNumber;
        
    } catch (Exception $e) {
        error_log("Error in generateTransactionFromId: " . $e->getMessage());
        return null;
    }
}

function generateTransactionFromCustomerId($customer_id) {
    try {
        // Generate transaction number from customer_id (alternative method)
        if (empty($customer_id) || !is_numeric($customer_id)) {
            throw new Exception("Invalid customer_id provided");
        }
        
        $transactionNumber = 'CCR-' . str_pad($customer_id, 2, '0', STR_PAD_LEFT);
        
        return $transactionNumber;
        
    } catch (Exception $e) {
        error_log("Error in generateTransactionFromCustomerId: " . $e->getMessage());
        return null;
    }
}

function generateRandomTransactionNumber() {
    try {
        // Generate random transaction number with timestamp
        $randomNumber = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
        return 'CCR-' . $randomNumber;
        
    } catch (Exception $e) {
        error_log("Error generating random transaction number: " . $e->getMessage());
        return null;
    }
}

function generateTimestampBasedTransaction() {
    try {
        // Generate transaction number based on current time
        $timeNumber = date('His'); // HHMMSS format
        $shortNumber = substr($timeNumber, -2); // Last 2 digits
        return 'CCR-' . $shortNumber;
        
    } catch (Exception $e) {
        error_log("Error generating timestamp-based transaction: " . $e->getMessage());
        return null;
    }
}

function generateSessionBasedTransaction() {
    try {
        // Generate transaction number using session ID
        $sessionId = session_id();
        $shortId = substr(md5($sessionId), 0, 2); // First 2 chars of MD5
        $numericId = str_pad(hexdec($shortId) % 100, 2, '0', STR_PAD_LEFT);
        return 'CCR-' . $numericId;
        
    } catch (Exception $e) {
        error_log("Error generating session-based transaction: " . $e->getMessage());
        return null;
    }
}

// Usage examples:
/*
// Method 1: Generate transaction number BEFORE inserting into transaction table
// Then INSERT with this number, get transaction_id from lastInsertId()
$transactionNumber = generateTransactionNumber($conn);
// INSERT INTO transaction (customer_id, transaction_no, status, created_at) VALUES (?, ?, ?, NOW())
// $transaction_id = $conn->lastInsertId();

// Method 2: Generate AFTER getting transaction_id from INSERT
// $stmt = $conn->prepare("INSERT INTO transaction (customer_id, status, created_at) VALUES (?, ?, NOW())");
// $stmt->execute([$customer_id, 'Pending']);
// $transaction_id = $conn->lastInsertId();
// $transactionNumber = generateTransactionFromId($transaction_id);
// Then UPDATE: UPDATE transaction SET transaction_no = ? WHERE transaction_id = ?

// Method 3: Generate from customer_id (if needed)
// $transactionNumber = generateTransactionFromCustomerId($customer_id);

// Method 4: Random/timestamp based
// $transactionNumber = generateRandomTransactionNumber();
*/
?>