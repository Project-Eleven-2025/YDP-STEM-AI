<?php
$host = "localhost";
$dbname = "masterlist_db";
$user = "postgres";
$pass = "ydp-stem-ai";  // Change this!

try {
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Connected to PostgreSQL successfully!<br>";

    $stmt = $pdo->query("SELECT * FROM masterlist");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "User: " . $row['username'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
