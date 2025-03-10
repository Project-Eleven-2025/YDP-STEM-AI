<?php
session_start();
header("Content-Type: application/json");

$host = "localhost";
$dbname = "masterlist_db";
$user = "ydp-stem";
$password = "project2025";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Ensure Only Superadmins role Can Enroll New Admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$superAdminID = $_SESSION['admin_id'];  // Get logged-in Superadmin ID

//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');

    if (empty($username) || empty($email) || empty($password) || empty($fname) || empty($lname)) {
        http_response_code(400);
        die(json_encode(['status' => 'error', 'message' => 'All fields are required']));
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM admin_info WHERE username = :username OR admin_emailadd = :email");
    $stmt->execute([':username' => $username, ':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        die(json_encode(['status' => 'error', 'message' => 'Username or email already exists']));
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    //Insert New Admin (Pending Status)
    $stmt = $conn->prepare("
        INSERT INTO admin_info (adminID, username, pass_hash, admin_emailadd, admin_phonenum, admin_fname, admin_lname, admin_mname, admin_status, admin_role, created_at)
        VALUES (:adminID, :username, :pass_hash, :admin_emailadd, :admin_phonenum, :admin_fname, :admin_lname, :admin_mname, 'pending', 'admin', NOW())
    ");
    $newAdminID = 'admin-' . time();  // Generate Unique Admin ID
    $stmt->execute([
        ':adminID' => $newAdminID,
        ':username' => $username,
        ':pass_hash' => $hashedPassword,
        ':admin_emailadd' => $email,
        ':admin_phonenum' => $phone,
        ':admin_fname' => $fname,
        ':admin_lname' => $lname,
        ':admin_mname' => $mname
    ]);
    logAdminAction($superAdminID, "Enrolled new admin (Pending)", $newAdminID, "admin");

    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'New admin added with pending status']);
}

function logAdminAction($adminID, $action, $userID, $role) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, user_id, role, details, timestamp) VALUES (:admin_id, :action, :user_id, :role, NULL, NOW())");
    $stmt->execute([':admin_id' => $adminID, ':action' => $action, ':user_id' => $userID, ':role' => $role]);
}
?>
