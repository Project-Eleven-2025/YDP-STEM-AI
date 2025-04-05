<?php
// Database configuration

$servername = "localhost";
$username = "ydp-stem";
$password = "project2025";
$dbname = "masterlist_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Reference for compatibility
$GLOBALS['pdo'] = &$pdo;
?>
