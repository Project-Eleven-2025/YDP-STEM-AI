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

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $conn->prepare("SELECT user_fname, user_mname, user_lname, user_nickname, username, user_emailadd, user_birthdate, user_phonenum, user_address, user_gender, user_school FROM user_info WHERE userID = :userID");
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
    
    $fields = ["user_fname", "user_mname", "user_lname", "user_nickname", "user_emailadd", "user_birthdate", "user_phonenum", "user_address", "user_gender", "user_school"];
    $updateData = array_map(function($field) use ($input) {
        return $input[$field] ?? ($field === "user_mname" || $field === "user_nickname" ? NULL : "");
    }, array_combine($fields, $fields));
    
    try {
        $stmt = $conn->prepare("UPDATE user_info SET user_fname = :user_fname, user_mname = :user_mname, user_lname = :user_lname, user_nickname = :user_nickname, user_emailadd = :user_emailadd, user_birthdate = :user_birthdate, user_phonenum = :user_phonenum, user_address = :user_address, user_gender = :user_gender, user_school = :user_school WHERE userID = :userID");
        $updateData[":userID"] = $userID;
        $stmt->execute($updateData);
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Update failed: " . $e->getMessage()]);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    try {
        $stmt = $conn->prepare("DELETE FROM user_info WHERE userID = :userID");
        $stmt->execute([":userID" => $userID]);
        session_destroy();
        echo json_encode(["success" => true, "message" => "Account deleted successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Deletion failed: " . $e->getMessage()]);
    }
    exit();
}

function getUserSettings($userID) {
    global $conn;

    $query = "SELECT * FROM user_settings WHERE userID = :userID";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":userID", $userID);
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$settings) {
        return [
            "show_full_name" => true, //default
            "show_email" => false     // default
        ];
    }

    if (!isset($settings["show_full_name"])) {
        $settings["show_full_name"] = true;
    }

    return $settings;
}
//use this function when ui is up
function updateUserSettings($userID, $settings) {
    global $conn;

    $query = "UPDATE user_settings SET 
                show_full_name = :show_full_name, 
                show_email = :show_email
              WHERE userID = :userID";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":userID", $userID);
    $stmt->bindParam(":show_full_name", $settings['show_full_name']);
    $stmt->bindParam(":show_email", $settings['show_email']);
    $stmt->execute();
}
?>