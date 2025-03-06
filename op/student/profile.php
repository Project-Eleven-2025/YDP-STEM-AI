<?php
// Assuming you have a database connection file
include 'db_connection.php';

// Start the session
session_start();

// Check if the user is logged in by verifying the session ID and cookies
if (!isset($_SESSION['session_id']) || !isset($_COOKIE['session_token'])) {
    //header('Location: login.php');
    //exit(); //commented out to allow testing
}

// Get the session ID and token from the session and cookies
$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : null;
$session_token = isset($_COOKIE['session_token']) ? $_COOKIE['session_token'] : null;

// Verify the session ID and token with the database
$query = "SELECT * FROM login_session_logs WHERE session_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $session_id);
$stmt->execute();
$result = $stmt->get_result();
$session = $result->fetch_assoc();

if (!$session) {
    //header('Location: login.php');
    //exit(); //commented out to allow testing
}

// Get the user ID from the session
$user_id = isset($session['user_id']) ? $session['user_id'] : null;
// Print out cookies for debugging purposes
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
if ($user_id) {
    // Fetch the student profile from the database
    $query = "SELECT * FROM user_info WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        echo "Profile not found.";
        echo "<a href='dashboard.php'>Go back to dashboard</a>";
        exit();
    }
} else {
    echo "User ID not found.";
    echo "<a href='dashboard.php'>Go back to dashboard</a>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="../css/ui.css">
</head>
<body>
    <div class="sidenav">
        <nav>
            <ul>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="assessment.php">Assessment</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="content">
        <h1>Student Profile</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($student['age']); ?></p>
        <p><strong>Major:</strong> <?php echo htmlspecialchars($student['major']); ?></p>
    </div>
</body>
</html>