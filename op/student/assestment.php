<?php
session_start();
if (!isset($_SESSION['sessionID']) || !isset($_COOKIE['PHPSESSID'])) {
    header('Location: ../login.html');
    exit(); //commented out to allow testing
}
$session_id = isset($_SESSION['sessionID']) ? $_SESSION['sessionID'] : null;
$query = "SELECT * FROM login_session_logs WHERE session_id= ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $session_id);
$stmt->execute();
$result = $stmt->get_result();
$session = $result->fetch_assoc();
if (!$session) {
    header('Location: ../login.html');
    exit(); //commented out to allow testing
}
$user_id = isset($session['user_id']) && preg_match('/^\d{4}-student-\d{10}-\d{4}-\d{4}$/', $session['user_id']) ? $session['user_id'] : null;
echo "<pre>";
echo "User ID: " . htmlspecialchars($user_id) . "\n";
echo "</pre>";
if (!$user_id) {
    echo "User ID not found.";
    echo "<a href='dashboard.php'>Go back to dashboard</a>";
    exit();
}
if ($user_id) {
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
$query = "SELECT * FROM quizzes WHERE user_id = ? ORDER BY date_taken DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$quizzes = $result->fetch_all(MYSQLI_ASSOC);

echo "<h2>Last Answered Quizzes</h2>";
if (count($quizzes) > 0) {
    echo "<ul>";
    foreach ($quizzes as $quiz) {
        echo "<li>" . htmlspecialchars($quiz['quiz_name']) . " - " . htmlspecialchars($quiz['score']) . "% on " . htmlspecialchars($quiz['date_taken']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No quizzes found.</p>";
}

$query = "SELECT * FROM assessments WHERE user_id = ? ORDER BY date_taken DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$assessments = $result->fetch_all(MYSQLI_ASSOC);

echo "<h2>Last Answered Assessments</h2>";
if (count($assessments) > 0) {
    echo "<ul>";
    foreach ($assessments as $assessment) {
        echo "<li>" . htmlspecialchars($assessment['assessment_name']) . " - " . htmlspecialchars($assessment['score']) . "% on " . htmlspecialchars($assessment['date_taken']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No assessments found.</p>";
}