<?php
session_start();

$host = "localhost";
$dbname = "masterlist_db";
$user = "ydp-stem";
$password = "project2025";

header("Content-Type: application/json");

$dsn = "mysql:host=$host;dbname=$dbname";
$conn = new PDO($dsn, $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function validate($tablename, $columnname, $value){
    global $conn;
    $sql = "SELECT * FROM $tablename WHERE $columnname = :value";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':value' => $value]);
    if ($stmt->rowCount() > 0) {
        return false;
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember-me']) ? $_POST['remember-me'] : 'false';
    $stmt = $conn->prepare("SELECT * FROM users WHERE uname = :username");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !isset($user['passphrase']) || empty($user['passphrase'])) {
        // Log the failed login attempt
        $device_os = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $sessionId = session_id();
        $stmt = $conn->prepare("INSERT INTO login_attempts (session_id, created_at, logged_out_at, user_id, device_os, ip_address) VALUES (:session_id, current_timestamp(), NULL, NULL, :device_os, :ip_address)");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':device_os', $device_os);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();

        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password 1',
            'provided_username' => $username
        ]);
        exit();
    } else {
        // Log the successful user retrieval
        $device_os = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $sessionId = session_id();
        $stmt = $conn->prepare("INSERT INTO login_attempts (session_id, created_at, logged_out_at, user_id, device_os, ip_address) VALUES (:session_id, current_timestamp(), NULL, :user_id, :device_os, :ip_address)");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':device_os', $device_os);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();
    }

    $stmt = $conn->prepare("SELECT passphrase FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
    $stmt->execute();
    $passphraseData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$passphraseData || !password_verify($password, $passphraseData['passphrase'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password 2',
            'provided_username' => $username
        ]);
        exit();
    }
    $_SESSION['user_id'] = $user['id'];
    echo json_encode(['status' => 'success', 'message' => 'Login successful']);

    setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
    setcookie('session_id', session_id(), time() + (86400 * 30), "/");

    if ($remember == 'true') {
        $sessionId = session_id();
        setcookie('session_id', $sessionId, time() + (86400 * 30), "/");

        $device_os = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $stmt = $conn->prepare("INSERT INTO login_attempts (session_id, created_at, logged_out_at, user_id, device_os, ip_address) VALUES (:session_id, current_timestamp(), NULL, :user_id, :device_os, :ip_address)");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':device_os', $device_os);
        $stmt->bindParam(':ip_address', $ip_address);
        $stmt->execute();
    }

    $userGroup = $user['user_group'];
    switch ($userGroup) {
        case 'student':
            header('Location: ../op/student/dashboard.php?session_id=' . session_id());
            exit();
        case 'teacher':
        case 'admin':
            header('Location: ../op/admin/dashboard.php?session_id=' . session_id());
            exit();
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid user group']);
            exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

