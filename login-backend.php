<html>
<?php
session_start();

header("Content-Type: application/json");

$response = ["success" => false, "message" => "Invalid username or password."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $username = $input["username"] ?? "";
    $password = $input["password"] ?? "";

    //checks data in data.json file (supports the register-backend if coded correctly)
    $file = "data.json";
    $existingData = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $validUser = null;
    foreach ($existingData as $userData) {
        if ($userData["username"] === $username) {
            $validUser = $userData;
            break;
        }
    }

    if ($validUser && $validUser["password"] === $password) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        $response = ["success" => true, "message" => "Login successful!", "username" => $username];
    }

 
    $logData = [
        "username" => $username,
        "success" => $response["success"],
        "timestamp" => date("Y-m-d H:i:s")
    ];

    $existingData[] = $logData;
    file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT));

    // Return the response
    echo json_encode($response);
    exit();
}
?>
</html>
