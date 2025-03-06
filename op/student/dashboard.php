<?php
session_start();

if (!isset($_SESSION['sessionID']) || !isset($_SESSION['session_start_time'])) {
    // Redirect to login page if sessionID or session_start_time is not set
    header("Location: ../login.html");
    exit();
}

$current_time = time();
$session_start_time = $_SESSION['session_start_time'];
$session_age = $current_time - $session_start_time;

if ($session_age > 86400) { // 86400 seconds = 24 hours
    // Session is older than 24 hours, destroy session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../login.html");
    exit();
}
include 'db_connection.php'; // Include your database connection file

$userID = $_SESSION['sessionID']; // Assuming sessionID is the userID
$query = "SELECT user_nickname FROM users WHERE userID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($user_nickname);
$stmt->fetch();
$stmt->close();
$conn->close();

if (!$user_nickname) {
    // If user_nickname is not found, redirect to login page
    header("Location: ../login.html");
    exit();
}
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
    include 'db_connection.php';
    $query = "SELECT COUNT(*) FROM quizzes WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $count;
}

function getLessonsFinished($userID) {
    include 'db_connection.php';
    $query = "SELECT COUNT(*) FROM lessons WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $count;
}

function getAssessmentsDone($userID) {
    include 'db_connection.php';
    $query = "SELECT COUNT(*) FROM assessments WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $count;
}

function getUserCertificates($userID) {
    include 'db_connection.php';
    $query = "SELECT COUNT(*) FROM certificates WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $count;
}

function getAccountAge($userID) {
    include 'db_connection.php';
    $query = "SELECT registration_date FROM users WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($registration_date);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    $current_date = new DateTime();
    $registration_date = new DateTime($registration_date);
    $interval = $current_date->diff($registration_date);
    return $interval->days;
}
?>
</body>
</html>