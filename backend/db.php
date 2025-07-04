<?php
$host = "localhost";
$dbname = "ccro_db";
$user = "postgres";
$pass = "1234";

try {
    $conn = new PDO("pgsql:host=$host; dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>