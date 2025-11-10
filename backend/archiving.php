<?php
$host = "localhost";
$dbname = "ccro_db";
$user = "postgres";
$pass = "1234";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname;options='--search_path=archiving'", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Alternatively, you can set the search_path after connection:
    // $conn->exec("SET search_path TO archiving");
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>