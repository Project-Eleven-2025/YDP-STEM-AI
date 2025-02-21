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

    $stmt = $conn->prepare("SELECT userID, username, password FROM masterlist WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["userID"] = $user["userID"];
        
        $response = ["success" => true, "message" => "Login successful!", "username" => $username];
    }
    
    echo json_encode($response);
    exit();
}