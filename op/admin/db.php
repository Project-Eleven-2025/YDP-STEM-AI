<?php
// Database configuration

$servername = "localhost";
$username = "ydp-stem"; // Fixed: Corrected username
$password = "project2025"; // Fixed: Corrected password
$dbname = "masterlist_db";

$host = $servername;
$database = $dbname;

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
