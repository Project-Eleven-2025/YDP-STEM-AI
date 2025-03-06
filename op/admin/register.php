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
    $passcode = $_POST['passcode'];
    $teacherID = $_POST['teacherID'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $teacher_emailadd = $_POST['teacher_emailadd'];
    $teacher_phonenum = $_POST['teacher_phonenum'];
    $teacher_fname = $_POST['teacher_fname'];
    $teacher_lname = $_POST['teacher_lname'];
    $teacher_mname = $_POST['teacher_mname'];
    $teacher_post_nominal = $_POST['teacher_post_nominal'];
    $teacher_birthdate = $_POST['teacher_birthdate'];
    $teacher_address = $_POST['teacher_address'];
    $teacher_gender = $_POST['teacher_gender'];
    $teacher_faculty = $_POST['teacher_faculty'];

    if ($passcode === ACCESS_PASSCODE) {

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
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
