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


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

// Fetch Filters & Pagination
$search = $_GET['search'] ?? '';
$adminID = $_GET['admin_id'] ?? '';
$action = $_GET['action'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Logs per page
$offset = ($page - 1) * $limit;

$query = "
    SELECT 
        a.log_id, a.admin_id, a.action, a.user_id, a.role, 
        DATE_FORMAT(a.timestamp, '%Y-%m-%d %H:%i:%s') AS formatted_timestamp,
        ad.username AS admin_username, ad.admin_emailadd AS admin_email
    FROM audit_logs a
    LEFT JOIN admin_info ad ON a.admin_id = ad.adminID
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $query .= " AND (a.user_id LIKE :search OR a.action LIKE :search OR ad.username LIKE :search OR ad.admin_emailadd LIKE :search)";
    $params['search'] = "%$search%";
}

if (!empty($adminID)) {
    $query .= " AND a.admin_id = :admin_id";
    $params['admin_id'] = $adminID;
}

if (!empty($action)) {
    $query .= " AND a.action = :action";
    $params['action'] = $action;
}

$query .= " ORDER BY a.timestamp DESC LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($query);
$stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

// Bind optional parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$auditLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'logs' => $auditLogs, 'page' => $page]);
?>
