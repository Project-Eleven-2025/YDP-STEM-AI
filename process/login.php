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

    $stmt = $conn->prepare("SELECT * FROM user_info WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !isset($user['pass_hash']) || !password_verify($password, $user['pass_hash'])) { // Fixed: Added check for 'pass_hash'
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        exit();
    }
    $_SESSION['user_id'] = $user['userID']; // Fixed: Corrected 'id' to 'userID'
    echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    
    if (isset($_GET['remember_me']) && $_GET['remember_me'] == '1') {
        $sessionId = session_id();
        setcookie('session_id', $sessionId, time() + (86400 * 30), "/"); // 86400 = 1 day, cookie lasts for 30 days

        // Log session ID, date created, and user who created it to database
        $stmt = $conn->prepare("INSERT INTO login_session_logs (session_id, user_id, date_created) VALUES (:session_id, :user_id, current_timestamp())");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $user['userID']);
        if ($stmt->execute()) {
            echo "Session logged successfully";
        } else {
            echo "Failed to log session";
        }
    }

    // Append session_id to URL
    $url = $_SERVER['REQUEST_URI'];
    $url .= (strpos($url, '?') === false ? '?' : '&') . 'session_id=' . session_id();
    
    if ($user && isset($user['userID'])) { // Fixed: Added check for 'userID'
        $userID = $user['userID']; // Fixed: Corrected 'user_id' to 'userID'
        $group = substr($userID, strpos($userID, '-') + 1, 1); // Assuming group is the first character after the first dash

        switch ($group) {
            case 's':
                echo json_encode(['userID' => $user['userID'], 'group' => substr($user['userID'], strpos($user['userID'], '-') + 1, 1)]);
                header("Location: ../op/student/dashboard.php?session_id=" . session_id());
                break;
            case 't':
                echo json_encode(['userID' => $user['userID'], 'group' => substr($user['userID'], strpos($user['userID'], '-') + 1, 1)]);
                header("Location: ../op/admin/dashboard.php?session_id=" . session_id());
                break;
            case 'a':
                echo json_encode(['userID' => $user['userID'], 'group' => substr($user['userID'], strpos($user['userID'], '-') + 1, 1)]);
                header("Location: ../op/admin/dashboard.php?session_id=" . session_id());
                break;
        }
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

