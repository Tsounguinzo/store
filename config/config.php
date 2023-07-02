<?php
// Database configuration
$db_host = "localhost";
$db_name = "shop_db";
$db_user = "root";
$db_password = "";

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);

    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    // Handle connection errors
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed: " . $e->getMessage());
}

$paths = [
    'root_path' => 'store',
    'public_path' => 'store/public',
    'views_path' => 'store/app/src/Views',
];