<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$host = "localhost";
$dbUsername = "postgres";
$dbPassword = "your_password";
$dbname = "masterlist_db";

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    try {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $conn = new PDO($dsn, $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()])); 
    }
}

$userID = $_SESSION["userID"];

// Fetch user profile information from user_info table
$query = "SELECT user_fname, user_mname, user_lname, user_nickname, username, user_emailadd FROM user_info WHERE userID = :userID";
$stmt = $conn->prepare($query);
$stmt->bindParam(":userID", $userID);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch milestones from user_milestone table
$query = "SELECT milestone_courseID, milestone_lesson, milestone_progress, milestone_status, milestone_certificate_userID, milestone_certificate_courseID, milestone_user_performance, milestone_date FROM user_milestone WHERE milestone_userID = :userID";
$stmt = $conn->prepare($query);
$stmt->bindParam(":userID", $userID);
$stmt->execute();
$milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate progress based on completed lessons, quizzes, and problem-solving tasks
foreach ($milestones as &$milestone) {
    $completed_lessons = getCompletedLessons($milestone['milestone_courseID'], $milestone['milestone_userID']);
    $completed_quizzes = getCompletedQuizzes($milestone['milestone_courseID'], $milestone['milestone_userID']);
    $completed_problem_solving = getCompletedProblemSolving($milestone['milestone_courseID'], $milestone['milestone_userID']);
    
    $total_lessons = getTotalLessons($milestone['milestone_courseID']);
    $total_quizzes = getTotalQuizzes($milestone['milestone_courseID']);
    $total_problem_solving = getTotalProblemSolving($milestone['milestone_courseID']);
    
    $total_tasks = $total_lessons + $total_quizzes + $total_problem_solving;
    $completed_tasks = $completed_lessons + $completed_quizzes + $completed_problem_solving;
    
    $milestone['milestone_progress'] = ($completed_tasks / $total_tasks) * 100;
    
    // Update the milestone status if progress reaches 100%
    if ($milestone['milestone_progress'] == 100) {
        $milestone['milestone_status'] = 'Completed';
    }
}

// Return profile data including user information and milestones
$profile = [
    "username" => $user["username"],
    "nickname" => $user["user_nickname"],
    "full_name" => $user["user_fname"] . " " . $user["user_mname"] . " " . $user["user_lname"],
    "email" => $user["user_emailadd"],
    "milestones" => $milestones
];

echo json_encode(["success" => true, "data" => $profile]);

exit();

// Helper functions to fetch the necessary data
function getCompletedLessons($courseID, $userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM lessons WHERE courseID = :courseID AND userID = :userID AND status = 'Completed'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->bindParam(":userID", $userID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getCompletedQuizzes($courseID, $userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM quizzes WHERE courseID = :courseID AND userID = :userID AND status = 'Completed'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->bindParam(":userID", $userID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getCompletedProblemSolving($courseID, $userID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM problem_solving WHERE courseID = :courseID AND userID = :userID AND status = 'Completed'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->bindParam(":userID", $userID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getTotalLessons($courseID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM lessons WHERE courseID = :courseID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getTotalQuizzes($courseID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM quizzes WHERE courseID = :courseID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function getTotalProblemSolving($courseID) {
    global $conn;
    $query = "SELECT COUNT(*) FROM problem_solving WHERE courseID = :courseID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":courseID", $courseID);
    $stmt->execute();
    return $stmt->fetchColumn();
}
?>
