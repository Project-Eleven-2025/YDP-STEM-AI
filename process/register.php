<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$host = "localhost";
$dbname = "master";  // Make sure this is correct
$user = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbname";
$conn = new PDO($dsn, $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mname = $_POST['mname'] ?? NULL;
    $nickname = $_POST['nickname'] ?? NULL;
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $school = $_POST['school'];
    $group = $_POST['group'];
    
    // Generate User ID
    $year = date("Y");
    $month_day = date("md"); // Get MMDD
    $userIndex = str_pad(rand(0, 9999999999), 10, "0", STR_PAD_LEFT);
    $uniqueID = rand(1000, 9999); // Unique control number (random 4-digit)
    $userID = "{$year}-{$group}{$userIndex}-{$uniqueID}-{$month_day}";

    $sql = "INSERT INTO user_info (
                userID, username, pass_hash, user_emailadd, user_phonenum, user_fname, 
                user_lname, user_mname, user_nickname, user_birthdate, user_address, user_gender, user_school
            ) 
            VALUES (
                :userID, :username, :pass_hash, :email, :phone, :fname, 
                :lname, :mname, :nickname, :birthdate, :address, :gender, :school
            )";

    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute([
            ':userID' => $userID,
            ':username' => $username,
            ':pass_hash' => $password,  // Fixed: Should match `pass_hash`
            ':email' => $email,
            ':phone' => $phone,  // Fixed: Should match `user_phonenum`
            ':fname' => $fname,
            ':lname' => $lname,
            ':mname' => $mname,
            ':nickname' => $nickname,
            ':birthdate' => $birthdate,  // Fixed: Corrected `$userbirthday`
            ':address' => $address,
            ':gender' => $gender,
            ':school' => $school
            
        ]);
        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die("SQL Error: " . $e->getMessage());
        
    }
    
    // Redirect after successful registration
    header("Location: ../login.html");
    exit();
}


// Fetch all users to display in the table
$query = $conn->query("SELECT * FROM user_info");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>