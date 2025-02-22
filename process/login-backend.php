<?php
session_start();
header("Content-Type: application/json");

$response = ["success" => false, "message" => "Invalid username or password."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $username = $input["username"] ?? "";
    $password = $input["password"] ?? "";
    
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
    
    //func to verify user login if student or teacher//

    function checkLogin($conn, $table, $idColumn, $usernameColumn, $passwordColumn) {
        global $username, $password;
        $stmt = $conn->prepare("SELECT $idColumn, $usernameColumn, $passwordColumn FROM $table WHERE $usernameColumn = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && hash_equals($user[$passwordColumn], hash("sha256", $password))) {
            return $user;
        }
        return null;
    }

    $user = checkLogin($conn, "user_info", "userID", "username", "pass_hash");

    if ($user) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["userID"] = $user["userID"];
        $_SESSION["role"] = "student";
        
        $response = ["success" => true, "message" => "Login successful!", "username" => $username, "role" => "student"];
    } else {
        $teacher = checkLogin($conn, "teachers_info", "teacherID", "username", "pass_hash");

        if ($teacher) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["userID"] = $teacher["teacherID"];
            $_SESSION["role"] = "teacher";
            
            $response = ["success" => true, "message" => "Login successful!", "username" => $username, "role" => "teacher"];
        }
    }

    echo json_encode($response);
    exit();
}
