<?php
session_start();

$host = "localhost";
$dbname = "masterlist_db";
$user = "ydp-stem";
$password = "project2025";

$dsn = "mysql:host=$host;dbname=$dbname";
$conn = new PDO($dsn, $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$username = $_POST['uname'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email = $_POST['email'];
$phone = $_POST['phone'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$mname = $_POST['mname'] ?? NULL;
$nickname = $_POST['nname'] ?? NULL;
$birthdate = $_POST['birthdate'];
$address = $_POST['address'];
$gender = $_POST['gender'];
$school = $_POST['school'];
$course = $_POST['course'];
$user_group = "student"; // Fixed: Added user group
$created_at = date("Y-m-d H:i:s");

function validate($tablename, $columnname, $value){
    global $conn;
    $sql = "SELECT * FROM $tablename WHERE $columnname = :value";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':value' => $value]);
    if ($stmt->rowCount() > 0) {
        return false;
    }
    return true;
}

function register_user(){
    global $conn, $username, $password, $email, $phone, $fname, $lname, $mname, $nickname, $birthdate, $address, $gender, $school, $course, $user_group, $created_at;
    $sql = "INSERT INTO users (
        fname, lname, mname, uname, nname, email, birthdate, address, phone, gender, course, school, passphrase, user_group, created_at
    ) 
    VALUES (
        :fname, :lname, :mname, :uname, :nname, :email, :birthdate, :address, :phone, :gender, :course, :school, :passphrase, :user_group, :created_at
    )";
    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':mname' => $mname,
            ':uname' => $username,
            ':nname' => $nickname,
            ':email' => $email,
            ':birthdate' => $birthdate,
            ':address' => $address,
            ':phone' => $phone,
            ':gender' => $gender,
            ':course' => $course,
            ':school' => $school,
            ':passphrase' => $password,
            ':user_group' => $user_group,
            ':created_at' => $created_at
        ]);
        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die("SQL Error: " . $e->getMessage());
    }
}

if (validate('users', 'uname', $username)) {
    register_user();
    header("Location: ../op/login.html");
    exit();
} else {
    echo "User already exists";
    echo "<a href='../op/login.html'>Go to Login</a>";
}

// Fetch all users to display in the table
$query = $conn->query("SELECT * FROM users");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>
