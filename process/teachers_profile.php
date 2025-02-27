<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "teacher") {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$host = "localhost";
$dbUsername = "postgres";
$dbPassword = "your_password";
$dbname = "masterlist_db";

try {
    // Connect to the database
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$teacherID = $_SESSION["userID"]; // Get the teacher ID from session

// Fetch teacher profile information
$query = "SELECT user_fname, user_mname, user_lname, user_nickname, username, user_emailadd, user_birthdate, user_phonenum, user_address, user_gender, user_school FROM teachers_info WHERE teacherID = :teacherID";
$stmt = $conn->prepare($query);
$stmt->bindParam(":teacherID", $teacherID);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch teacher settings
$querySettings = "SELECT show_full_name, show_email FROM teacher_settings WHERE teacherID = :teacherID";
$stmtSettings = $conn->prepare($querySettings);
$stmtSettings->bindParam(":teacherID", $teacherID);
$stmtSettings->execute();
$settings = $stmtSettings->fetch(PDO::FETCH_ASSOC);

if (!$settings) {
    $settings = [
        "show_full_name" => true,
        "show_email" => false
    ];
}

$profile = [
    "username" => $teacher["username"],
    "nickname" => $teacher["user_nickname"],
    "full_name" => $settings["show_full_name"] ? $teacher["user_fname"] . " " . $teacher["user_mname"] . " " . $teacher["user_lname"] : $teacher["user_nickname"],
    "email" => $settings["show_email"] ? $teacher["user_emailadd"] : "Hidden",
    "birthdate" => $teacher["user_birthdate"],
    "phone_number" => $teacher["user_phonenum"],
    "address" => $teacher["user_address"],
    "gender" => $teacher["user_gender"],
    "school" => $teacher["user_school"]
];

echo json_encode(["success" => true, "data" => $profile]);

exit();
?>
