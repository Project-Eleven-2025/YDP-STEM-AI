<?php
session_start();

$servername = "localhost";
$username = "ydp-stem"; // Fixed: Corrected username
$password = "project2025"; // Fixed: Corrected password
$dbname = "masterlist_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['sessionID'])) {
    if (isset($_COOKIE['session_id'])) {
        $_SESSION['sessionID'] = $_COOKIE['session_id'];
    } else {
        // If sessionID is not set, redirect to login page
        //header("Location: ../login.html");
        //exit();
    }
}

$sessionID = $_SESSION['sessionID']; // Store sessionID in a separate variable
$query = "SELECT user_id, date_created FROM login_session_logs WHERE session_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $sessionID);
$stmt->execute();
$stmt->bind_result($userID, $date_created);
$stmt->fetch();
$stmt->close();

if (!$date_created) {
    // If date_created is not retrieved, redirect to login page
    echo "Session expired. Please log in again.";
    //header("Location: ../login.html");
    //exit();
}

$current_time = time();
$session_start_time = strtotime($date_created);
$session_age = $current_time - $session_start_time;

if ($session_age > 86400) { // 86400 seconds = 24 hours
    // Session is older than 24 hours, destroy session and redirect to login page
    session_unset();
    session_destroy();
    echo "Session expired. Please log in again.";
    //header("Location: ../login.html");
    //exit();
}

$query = "SELECT user_nickname FROM user_info WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    echo "User not found.";
    // If user_nickname is not found, redirect to login page
    //header("Location: ../login.html");
    //exit();
}
$stmt->bind_result($user_nickname);
$stmt->fetch();
$stmt->close();
$conn->close();

// Session is valid, continue with the rest of the dashboard code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user_nickname); ?>!</h1>
    <nav>
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="assessment.php">Assessment</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
        </ul>
    </nav>
    <div class="milestone-tracker">
        <h2>Milestone Tracker</h2>
        <ul>
            <li>Quizzes Completed: <?php echo getQuizzesCompleted($userID); ?></li>
            <li>Lessons Finished: <?php echo getLessonsFinished($userID); ?></li>
            <li>Assessments Done: <?php echo getAssessmentsDone($userID); ?></li>
            <li>User Certificates: <?php echo getUserCertificates($userID); ?></li>
            <li>Account Age: <?php echo getAccountAge($userID); ?> days</li>
        </ul>
    </div>

<?php
function getQuizzesCompleted($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM quizzes WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getLessonsFinished($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM lessons WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getAssessmentsDone($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM assessments WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getUserCertificates($userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM certificates WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getAccountAge($userID) {
    global $conn;
    $query = "SELECT registration_date FROM user_info WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userID); // Fixed: Corrected parameter type to string
    $stmt->execute();
    $stmt->bind_result($registration_date);
    $stmt->fetch();
    $stmt->close();
    $current_date = new DateTime();
    $registration_date = new DateTime($registration_date);
    $interval = $current_date->diff($registration_date);
    return $interval->days;
}
?>
</body>
</html>