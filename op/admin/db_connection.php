<?php
$servername = "localhost";
$username = "ydp-stem";
$password = "project2025";
$dbname = "masterlist_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>