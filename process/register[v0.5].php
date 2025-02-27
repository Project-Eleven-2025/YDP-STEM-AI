<?php
session_start();

$host = "localhost";
$dbname = "master";  // Make sure this is correct
$user = "root";
$password = "";

try {
    // Try to connect to PostgreSQL
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbType = 'PostgreSQL';
} catch (PDOException $e) {
    try {
        // If PostgreSQL connection fails, try to connect to MySQL
        $dsn = "mysql:host=$host;dbname=$dbname";
        $conn = new PDO($dsn, $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbType = 'MySQL';
    } catch (PDOException $e) {
        echo "Could not connect to any database server: " . $e->getMessage();
        die();
    }
}

if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userbirthday = $_POST['userbirthday'];
    $email = $_POST['email'];  // Get the email input
    $group = $_POST['group']; // Get the selected group
    
    // Generate User ID
    $year = date("Y");
    $month_day = date("md"); // Get MMDD
    $userIndex = str_pad(rand(0, 9999999999), 10, "0", STR_PAD_LEFT); // 10-digit random number
    $uniqueID = rand(1000, 9999); // Unique control number (random 4-digit)
    $userID = "{$year}-{$group}{$userIndex}-{$uniqueID}-{$month_day}";

    $sql = "INSERT INTO masterlist (userID, fname, lname, username, password, userbirthday, email) 
            VALUES (:userID, :fname, :lname, :username, :password, :userbirthday, :email)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':userID' => $userID,
        ':fname' => $fname,
        ':lname' => $lname,
        ':username' => $username,
        ':password' => $password,
        ':userbirthday' => $userbirthday,
        ':email' => $email // Include email in the query
    ]);

    // Redirect to login.html after successful registration
    header("Location: ../login.html");
    exit();
}

// Handle user deletion
if (isset($_POST['delete'])) {
    $userID = $_POST['userID'];
    $sql = "DELETE FROM masterlist WHERE userID = :userID";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':userID' => $userID]);
}

// Fetch all users to display in the table
$query = $conn->query("SELECT * FROM masterlist");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>

