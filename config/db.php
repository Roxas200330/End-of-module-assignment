<?php
// db connection settings, change these if xampp uses a different login
$DB_HOST = 'localhost';
$DB_NAME = 'recipe_app';
$DB_USER = 'root';
$DB_PASS = '';

// pdo so we can use prepared statements
// errmode exception means bad queries throw instead of failing quietly
// fetch assoc gives rows as arrays keyed by column name
try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    // stop here if db is down, nothing else will work
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}
