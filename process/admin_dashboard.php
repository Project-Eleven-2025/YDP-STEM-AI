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
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$adminID = $_SESSION['admin_id']; 

try {
    // Fetches all Statistics
    $stmt = $conn->prepare("
        SELECT 'users' AS category, COUNT(*) AS total FROM user_info
        UNION ALL
        SELECT 'teachers', COUNT(*) FROM teachers_info
        UNION ALL
        SELECT 'admins', COUNT(*) FROM admin_info WHERE admin_role = 'admin'
        UNION ALL
        SELECT 'superadmins', COUNT(*) FROM admin_info WHERE admin_role = 'superadmin'
        UNION ALL
        SELECT 'reports', COUNT(*) FROM reports
        UNION ALL
        SELECT 'login_logs', COUNT(*) FROM login_session_logs
    ");
    $stmt->execute();
    
    // 🔹 Fetch Results into an Associative Array
    $stats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stats[$row['category']] = $row['total'];
    }
e
    echo json_encode([
        'status' => 'success',
        'total_users' => $stats['users'] ?? 0,
        'total_teachers' => $stats['teachers'] ?? 0,
        'total_admins' => $stats['admins'] ?? 0,
        'total_superadmins' => $stats['superadmins'] ?? 0,
        'total_reports' => $stats['reports'] ?? 0,
        'total_logs' => $stats['login_logs'] ?? 0
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
}
?>
