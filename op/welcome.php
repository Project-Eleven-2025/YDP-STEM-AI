<?php
session_start();

// Database connection
$servername = "localhost";
$username = "";
$password = "";
$dbname = "masterlist";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if session ID and user ID are set
if (isset($_SESSION['session_id']) && isset($_SESSION['user_id'])) {
    $session_id = $_SESSION['session_id'];
    $user_id = $_SESSION['user_id'];

    // Prepare and execute SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE session_id = ? AND user_id = ?");
    $stmt->bind_param("si", $session_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        echo "<h1>Welcome, " . htmlspecialchars($user['username']) . "!</h1>";
        // Display dashboard content here
    } else {
        echo "<h1>Invalid session. Please log in again.</h1>";
    }

    $stmt->close();
} else {
    echo "<h1>No session found. Please log in.</h1>";
}

$conn->close();
?>