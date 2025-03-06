<?php
session_start();

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo "alert('User is not logged in. Redirecting to login page in 3 seconds...');";
    sleep(3);
    header("Location: login.html");
}

// Log the session ID and logout time in the database
$sessionId = session_id();
$loggedOutAt = date('Y-m-d H:i:s');
$userId = $_SESSION['user_id']; // Assuming user_id is stored in session

// Database connection
$servername = "localhost";
$username = "ydp-stem";
$password = "project2025";
$dbname = "masterlist_db"; // Update this to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the logout time in the database
$sql = "UPDATE login_session_logs SET logged_out_at='$loggedOutAt' WHERE session_id='$sessionId' AND user_id='$userId'";

if ($conn->query($sql) === TRUE) {
    // Successfully logged out
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();

// Function to log out the user
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Clear login cookies
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    // Destroy the session
    session_destroy();

    // Redirect to login page or home page
    header("Location: login.html");
    exit();
}

// Call the logout function
logout();
?>