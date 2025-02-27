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
        // Error connecting to the database
        echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
        exit();
    }

    // Function to verify user login (student or teacher)
    function checkLogin($conn, $table, $idColumn, $usernameColumn, $passwordColumn) {
        global $username, $password;
        
        try {
            $stmt = $conn->prepare("SELECT $idColumn, $usernameColumn, $passwordColumn FROM $table WHERE $usernameColumn = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

    
            if ($user && hash_equals($user[$passwordColumn], hash("sha256", $password))) {
                return $user;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Query error: " . $e->getMessage()]);
            exit();
        }
    }

    $user = checkLogin($conn, "user_info", "userID", "username", "pass_hash");

    if ($user) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["userID"] = $user["userID"];
        $_SESSION["role"] = "student";
        
        // Redirect to dashboard for students, change it
        header("Location: dashboard.php");
        exit();
    } else {
        $teacher = checkLogin($conn, "teachers_info", "teacherID", "username", "pass_hash");

        if ($teacher) {
            $_SESSION["loggedin"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["userID"] = $teacher["teacherID"];
            $_SESSION["role"] = "teacher";
            
            // Redirect to dashboard for teachers,change it
            header("Location: dashboard.php");
            exit();
        } else {
            // If both student and teacher are not found, show an error
            $response["message"] = "Invalid username or password.";
        }
    }

    echo json_encode($response);
    exit();
}
