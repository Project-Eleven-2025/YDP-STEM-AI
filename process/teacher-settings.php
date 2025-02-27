<?php
session_start();
header("Content-Type: application/json");

// Ensure that only teachers have access
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "teacher") {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

$host = "localhost";
$dbUsername = "postgres";
$dbPassword = "your_password";
$dbname = "masterlist_db";

try {
    // Connect to the database
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$teacherID = $_SESSION["userID"]; 

// Handle GET request (Fetch teacher settings and info)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Fetch teacher settings
    $querySettings = "SELECT show_full_name, show_email FROM teacher_settings WHERE teacherID = :teacherID";
    $stmtSettings = $conn->prepare($querySettings);
    $stmtSettings->bindParam(":teacherID", $teacherID);
    $stmtSettings->execute();
    $settings = $stmtSettings->fetch(PDO::FETCH_ASSOC);

    // Fetch teacher general info
    $queryInfo = "SELECT teacher_fname, teacher_mname, teacher_lname, teacher_nickname, teacher_emailadd, teacher_phonenum, teacher_address, teacher_gender, teacher_school FROM teachers_info WHERE teacherID = :teacherID";
    $stmtInfo = $conn->prepare($queryInfo);
    $stmtInfo->bindParam(":teacherID", $teacherID);
    $stmtInfo->execute();
    $teacherInfo = $stmtInfo->fetch(PDO::FETCH_ASSOC);

    if ($settings && $teacherInfo) {
        echo json_encode([
            "success" => true, 
            "data" => [
                "settings" => [
                    "show_full_name" => $settings['show_full_name'],
                    "show_email" => $settings['show_email']
                ],
                "teacher_info" => [
                    "teacher_fname" => $teacherInfo['teacher_fname'],
                    "teacher_mname" => $teacherInfo['teacher_mname'],
                    "teacher_lname" => $teacherInfo['teacher_lname'],
                    "teacher_nickname" => $teacherInfo['teacher_nickname'],
                    "teacher_emailadd" => $teacherInfo['teacher_emailadd'],
                    "teacher_phonenum" => $teacherInfo['teacher_phonenum'],
                    "teacher_address" => $teacherInfo['teacher_address'],
                    "teacher_gender" => $teacherInfo['teacher_gender'],
                    "teacher_school" => $teacherInfo['teacher_school']
                ]
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Teacher settings or info not found."]);
    }
    exit();
}

// Handle POST request (Update settings and teacher info)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    // Extract settings
    $settings = [
        "show_full_name" => isset($input["show_full_name"]) ? (bool)$input["show_full_name"] : true,
        "show_email" => isset($input["show_email"]) ? (bool)$input["show_email"] : false,
    ];

    // Extract teacher info
    $teacherInfo = [
        "teacher_fname" => isset($input["teacher_fname"]) ? $input["teacher_fname"] : "",
        "teacher_mname" => isset($input["teacher_mname"]) ? $input["teacher_mname"] : "",
        "teacher_lname" => isset($input["teacher_lname"]) ? $input["teacher_lname"] : "",
        "teacher_nickname" => isset($input["teacher_nickname"]) ? $input["teacher_nickname"] : "",
        "teacher_emailadd" => isset($input["teacher_emailadd"]) ? $input["teacher_emailadd"] : "",
        "teacher_phonenum" => isset($input["teacher_phonenum"]) ? $input["teacher_phonenum"] : "",
        "teacher_address" => isset($input["teacher_address"]) ? $input["teacher_address"] : "",
        "teacher_gender" => isset($input["teacher_gender"]) ? $input["teacher_gender"] : "",
        "teacher_school" => isset($input["teacher_school"]) ? $input["teacher_school"] : ""
    ];

    try {
        // Update teacher info in teachers_info
        $query1 = "UPDATE teachers_info SET 
                    teacher_fname = :teacher_fname, 
                    teacher_mname = :teacher_mname, 
                    teacher_lname = :teacher_lname, 
                    teacher_nickname = :teacher_nickname, 
                    teacher_emailadd = :teacher_emailadd, 
                    teacher_phonenum = :teacher_phonenum, 
                    teacher_address = :teacher_address, 
                    teacher_gender = :teacher_gender, 
                    teacher_school = :teacher_school 
                  WHERE teacherID = :teacherID";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bindParam(":teacherID", $teacherID);
        foreach ($teacherInfo as $key => $value) {
            $stmt1->bindParam(":$key", $value);
        }
        $stmt1->execute();


        $query2 = "UPDATE teacher_settings SET 
                    show_full_name = :show_full_name, 
                    show_email = :show_email
                  WHERE teacherID = :teacherID";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(":teacherID", $teacherID);
        $stmt2->bindParam(":show_full_name", $settings['show_full_name']);
        $stmt2->bindParam(":show_email", $settings['show_email']);
        $stmt2->execute();

        echo json_encode(["success" => true, "message" => "Settings and info updated successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Update failed: " . $e->getMessage()]);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    try {
        // Delete teacher info and settings
        $stmt1 = $conn->prepare("DELETE FROM teachers_info WHERE teacherID = :teacherID");
        $stmt1->execute([":teacherID" => $teacherID]);

        $stmt2 = $conn->prepare("DELETE FROM teacher_settings WHERE teacherID = :teacherID");
        $stmt2->execute([":teacherID" => $teacherID]);

        session_destroy();
        echo json_encode(["success" => true, "message" => "Account deleted successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Deletion failed: " . $e->getMessage()]);
    }
    exit();
}
?>
