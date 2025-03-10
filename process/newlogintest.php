<?php
session_start();

$host = "localhost";
$dbname = "masterlist_db";
$user = "ydp-stem";
$password = "project2025";

header("Content-Type: application/json");

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember-me']) ? $_POST['remember-me'] : false;

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit;
    }

    // 🔹 Check Admin Login
    $stmt = $conn->prepare("SELECT * FROM admin_info WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['pass_hash'])) {
        loginUser($admin['adminID'], 'admin', '../op/admin/admin_dashboard.php', $remember);
    }

    // 🔹 Check Student Login
    $stmt = $conn->prepare("SELECT * FROM user_info WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['pass_hash'])) {
        loginUser($user['userID'], 'student', '../op/student/dashboard.php', $remember);
    }

    // 🔹 Check Teacher Login
    $stmt = $conn->prepare("SELECT * FROM teachers_info WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher && password_verify($password, $teacher['pass_hash'])) {
        loginUser($teacher['teacherID'], 'teacher', '../op/teacher/teacher_dashboard.php', $remember);
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
}

// Function to Log in Users
function loginUser($userID, $role, $redirect, $remember) {
    global $conn;

    $_SESSION['user_id'] = $userID;
    $_SESSION['role'] = $role;

    // Set cookies for persistent login
    if ($remember == 'true') {
        setcookie('user_id', $userID, time() + (86400 * 30), "/");
        setcookie('session_id', session_id(), time() + (86400 * 30), "/");
    }

    logSession($userID, $role);
    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
    exit();
}

// Function to Log Session Details
function logSession($userID, $role) {
    global $conn;
    $sessionId = session_id();
    $device_os = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $stmt = $conn->prepare("INSERT INTO login_session_logs (session_id, created_at, logged_out_at, user_id, device_os, ip_address) 
                            VALUES (:session_id, NOW(), NULL, :user_id, :device_os, :ip_address)");
    $stmt->execute([
        ':session_id' => $sessionId,
        ':user_id' => $userID,
        ':device_os' => $device_os,
        ':ip_address' => $ip_address
    ]);
}
?>
