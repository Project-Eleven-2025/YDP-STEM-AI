<?php
session_start();

$host = 'localhost';
$dbname = 'your_database_name';
$user = 'your_database_user';
$password = 'your_database_password';

try {
    // Try to connect to PostgreSQL
    $dsn = "pgsql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $dbType = 'PostgreSQL';
} catch (PDOException $e) {
    try {
        // If PostgreSQL connection fails, try to connect to MySQL
        $dsn = "mysql:host=$host;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $password);
        $dbType = 'MySQL';
    } catch (PDOException $e) {
        die("Could not connect to any database server: " . $e->getMessage());
    }
}

// Assuming you have a form that posts 'username' and 'password'
$username = $_POST['username'];
$password = $_POST['password'];

// Query to check user credentials
$query = "SELECT * FROM users WHERE username = :username AND password = :password";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // User authenticated, create session
    $_SESSION['user_id'] = session_id();
    $_SESSION['username'] = $username;
    $_SESSION['db_type'] = $dbType;

    // Log session ID
    $logFile = 'session_log.txt';
    $logMessage = "Session ID: " . session_id() . " - Username: $username - Database: $dbType\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    echo "Invalid username or password.";
}
?>