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

// Define the passcode
define('ACCESS_PASSCODE', '123');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $passcode = htmlspecialchars(trim($_POST['passcode']));
    $teacherID = htmlspecialchars(trim($_POST['teacherID']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $teacher_emailadd = filter_var(trim($_POST['teacher_emailadd']), FILTER_SANITIZE_EMAIL);
    $teacher_phonenum = htmlspecialchars(trim($_POST['teacher_phonenum']));
    $teacher_fname = htmlspecialchars(trim($_POST['teacher_fname']));
    $teacher_lname = htmlspecialchars(trim($_POST['teacher_lname']));
    $teacher_mname = htmlspecialchars(trim($_POST['teacher_mname']));
    $teacher_post_nominal = htmlspecialchars(trim($_POST['teacher_post_nominal']));
    $teacher_birthdate = htmlspecialchars(trim($_POST['teacher_birthdate']));
    $teacher_address = htmlspecialchars(trim($_POST['teacher_address']));
    $teacher_gender = htmlspecialchars(trim($_POST['teacher_gender']));
    $teacher_faculty = htmlspecialchars(trim($_POST['teacher_faculty']));

    if ($passcode === ACCESS_PASSCODE) {

        // Validate email format
        if (!filter_var($teacher_emailadd, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address!";
            exit;
        }

        // Validate required fields
        if (empty($teacherID) || empty($username) || empty($password) || empty($teacher_emailadd)) {
            echo "All required fields must be filled!";
            exit;
        }

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO teacher_info (teacherID, username, pass_hash, teacher_emailadd, teacher_phonenum, teacher_fname, teacher_lname, teacher_mname, teacher_post_nominal, teacher_birthdate, teacher_address, teacher_gender, teacher_faculty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $teacherID, $username, $hashed_password, $teacher_emailadd, $teacher_phonenum, $teacher_fname, $teacher_lname, $teacher_mname, $teacher_post_nominal, $teacher_birthdate, $teacher_address, $teacher_gender, $teacher_faculty);

        if ($stmt->execute()) {
            echo "Admin registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid passcode!";
    }
}
?>
