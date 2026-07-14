<?php
// Database configuration using PDO
require_once __DIR__ . '/security.php';
$host = '127.0.0.1';
$db   = 'englobedocs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // For production, log errors instead of echoing
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}
