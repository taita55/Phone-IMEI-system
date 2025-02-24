<?php
// config.php
// Database connection settings
$host = "localhost";
$dbname = "phone_imei_system";
$user = "root";  // adjust if needed
$pass = "";      // adjust if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session
if (!isset($_SESSION)) {
    session_start();
}
?>
