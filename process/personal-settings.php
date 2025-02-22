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
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]));
}

$userID = $_SESSION["userID"];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->prepare("SELECT user_fname, user_lname, username, user_emailadd, user_birthdate, user_phonenum, user_address, user_gender, user_school FROM user_info WHERE userID = :userID");
    $stmt->bindParam(":userID", $userID);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode(["success" => true, "data" => $user]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $fname = $input["user_fname"] ?? "";
    $lname = $input["user_lname"] ?? "";
    $email = $input["user_emailadd"] ?? "";
    $birthdate = $input["user_birthdate"] ?? "";
    $phonenum = $input["user_phonenum"] ?? "";
    $address = $input["user_address"] ?? "";
    $gender = $input["user_gender"] ?? "";
    $school = $input["user_school"] ?? "";

    $stmt = $conn->prepare("UPDATE user_info SET user_fname = :fname, user_lname = :lname, user_emailadd = :email, user_birthdate = :birthdate, user_phonenum = :phonenum, user_address = :address, user_gender = :gender, user_school = :school WHERE userID = :userID");
    $stmt->execute([
        ":fname" => $fname,
        ":lname" => $lname,
        ":email" => $email,
        ":birthdate" => $birthdate,
        ":phonenum" => $phonenum,
        ":address" => $address,
        ":gender" => $gender,
        ":school" => $school,
        ":userID" => $userID
    ]);

    echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $stmt = $conn->prepare("DELETE FROM user_info WHERE userID = :userID");
    $stmt->execute([":userID" => $userID]);
    
    session_destroy();
    echo json_encode(["success" => true, "message" => "Account deleted successfully."]);
    exit();
}
?>
