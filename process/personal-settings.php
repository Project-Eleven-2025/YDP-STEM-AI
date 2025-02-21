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
    $stmt = $conn->prepare("SELECT fname, lname, username, email, userbirthday FROM masterlist WHERE userID = :userID");
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
    
    $fname = $input["fname"] ?? "";
    $lname = $input["lname"] ?? "";
    $email = $input["email"] ?? "";
    $userbirthday = $input["userbirthday"] ?? "";

    $stmt = $conn->prepare("UPDATE masterlist SET fname = :fname, lname = :lname, email = :email, userbirthday = :userbirthday WHERE userID = :userID");
    $stmt->execute([
        ":fname" => $fname,
        ":lname" => $lname,
        ":email" => $email,
        ":userbirthday" => $userbirthday,
        ":userID" => $userID
    ]);

    echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $stmt = $conn->prepare("DELETE FROM masterlist WHERE userID = :userID");
    $stmt->execute([":userID" => $userID]);
    session_destroy();
    echo json_encode(["success" => true, "message" => "Account deleted successfully."]);
    exit();
}
?>
