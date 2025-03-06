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
$group = $_POST['group'];

$year = date("Y");
$month_day = date("md"); // Get MMDD
$userIndex = str_pad(rand(0, 9999999999), 10, "0", STR_PAD_LEFT);
$uniqueID = rand(1000, 9999); // Unique control number (random 4-digit)
$userID = "{$year}-{$group}{$userIndex}-{$uniqueID}-{$month_day}";

echo "User ID: $userID<br>";
echo "Username: $username<br>";
echo "Password: $password<br>";
echo "Email: $email<br>";
echo "Phone: $phone<br>";
echo "First Name: $fname<br>";
echo "Last Name: $lname<br>";
echo "Middle Name: $mname<br>";
echo "Nickname: $nickname<br>";
echo "Birthdate: $birthdate<br>";

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
    global $conn, $userID, $username, $password, $email, $phone, $fname, $lname, $mname, $nickname, $birthdate, $address, $gender, $school;
    $sql = "INSERT INTO user_info (
        userID, username, pass_hash, user_emailadd, user_phonenum, user_fname, 
        user_lname, user_mname, user_nickname, user_birthdate, user_address, user_gender, user_school
    ) 
    VALUES (
        :userID, :username, :pass_hash, :user_email, :user_phone, :user_fname, 
        :user_lname, :user_mname, :user_nickname, :user_birthdate, :user_address, :user_gender, :user_school
    )";
    echo "SQL: $sql<br>";
    $stmt = $conn->prepare($sql);
    try {
        $stmt->execute([
            ':userID' => $userID,
            ':username' => $username,
            ':pass_hash' => $password,  // Fixed: Should match `pass_hash`
            ':user_email' => $email,
            ':user_phone' => $phone,  // Fixed: Should match `user_phonenum`
            ':user_fname' => $fname,
            ':user_lname' => $lname,
            ':user_mname' => $mname,
            ':user_nickname' => $nickname,
            ':user_birthdate' => $birthdate,  // Fixed: Corrected `$userbirthday`
            ':user_address' => $address,
            ':user_gender' => $gender,
            ':user_school' => $school
        ]);
        echo "New record created successfully";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die("SQL Error: " . $e->getMessage());
    }
}

if (validate('user_info', 'username', $username)) {
    register_user();
    header("Location: ../op/login.html");
    exit();
} else {
    echo "User already exists";
    echo "<a href='../op/login.html'>Go to Login</a>";
}

// Fetch all users to display in the table
$query = $conn->query("SELECT * FROM user_info");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>