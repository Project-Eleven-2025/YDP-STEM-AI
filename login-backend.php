<?php
session_start();
header("Content-Type: application/json");

$response = ["success" => false, "message" => "Invalid username or password."];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $username = $input["username"] ?? "";
    $password = $input["password"] ?? "";
    
    $servername = "localhost";  
    $dbUsername = "root"; 
    $dbPassword = "";  
    $dbname = "masterlist_db";  

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    
    if ($conn->connect_error) {
        die(json_encode(["success" => false, "message" => "Database connection failed."]));
    }

    $stmt = $conn->prepare("SELECT userID, username, password FROM masterlist WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user["password"])) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["userID"] = $user["userID"];
            
            $response = ["success" => true, "message" => "Login successful!", "username" => $username];
        }
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
    exit();
}
