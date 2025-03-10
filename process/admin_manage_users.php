<?php
session_start();

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
    header("Location: ../login.php");
    exit();
}

$adminID = $_SESSION['admin_id'];  // Logged-in admin/superadmin

// Fetch Users with Filters & Pagination
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = $_GET['search'] ?? '';
    $role = $_GET['role'] ?? '';
    $status = $_GET['status'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // 🛠 Query users separately per role to apply correct filtering
    $query = "
        SELECT userID AS id, username, user_emailadd AS email, 'student' AS role, status, 
               (SELECT MAX(created_at) FROM login_session_logs WHERE userID = login_session_logs.user_id) AS last_login
        FROM user_info
        WHERE 1=1
    ";

    if (!empty($search)) {
        $query .= " AND (username LIKE :search OR user_emailadd LIKE :search)";
    }
    if (!empty($status)) {
        $query .= " AND status = :status";
    }

    $query .= " UNION ";

    $query .= "
        SELECT teacherID AS id, username, teacher_emailadd AS email, 'teacher' AS role, status, 
               (SELECT MAX(created_at) FROM login_session_logs WHERE teacherID = login_session_logs.user_id) AS last_login
        FROM teachers_info
        WHERE 1=1
    ";

    if (!empty($search)) {
        $query .= " AND (username LIKE :search OR teacher_emailadd LIKE :search)";
    }
    if (!empty($status)) {
        $query .= " AND status = :status";
    }

    // **Superadmins see all admins, admins only see non-superadmins**
    if ($_SESSION['role'] === 'superadmin') {
        $query .= " UNION 
            SELECT adminID AS id, username, admin_emailadd AS email, admin_role AS role, admin_status AS status, 
                   (SELECT MAX(created_at) FROM login_session_logs WHERE adminID = login_session_logs.user_id) AS last_login
            FROM admin_info
            WHERE 1=1";
    } else {
        $query .= " UNION 
            SELECT adminID AS id, username, admin_emailadd AS email, admin_role AS role, admin_status AS status, 
                   (SELECT MAX(created_at) FROM login_session_logs WHERE adminID = login_session_logs.user_id) AS last_login
            FROM admin_info
            WHERE admin_role != 'superadmin'";
    }

    $query .= " ORDER BY username ASC LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($query);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    if (!empty($status)) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'users' => $users, 'page' => $page]);
}

// Change Admin Role (`pending` to `active`)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'approve_admin') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid admin ID']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE admin_info SET admin_status = 'active' WHERE adminID = :id");
    $stmt->execute([':id' => $id]);

    logAdminAction($adminID, "Approved Admin", $id, "admin");
    echo json_encode(['status' => 'success', 'message' => 'Admin approved successfully']);
}

// Deactivate User Instead of Deleting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'deactivate') {
    $id = $_POST['id'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($id) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID or role']);
        exit();
    }

    $table = getUserTable($role);
    $idColumn = getUserIdColumn($role);

    $stmt = $conn->prepare("UPDATE $table SET status = 'inactive' WHERE $idColumn = :id");
    $stmt->execute([':id' => $id]);

    logAdminAction($adminID, "Deactivated User", $id, $role);
    echo json_encode(['status' => 'success', 'message' => 'User deactivated successfully']);
}

// Reactivate User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'reactivate') {
    $id = $_POST['id'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($id) || empty($role)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID or role']);
        exit();
    }

    $table = getUserTable($role);
    $idColumn = getUserIdColumn($role);

    $stmt = $conn->prepare("UPDATE $table SET status = 'active' WHERE $idColumn = :id");
    $stmt->execute([':id' => $id]);

    logAdminAction($adminID, "Reactivated User", $id, $role);
    echo json_encode(['status' => 'success', 'message' => 'User reactivated successfully']);
}

// Bulk Delete Users (experimental)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'bulk_delete') {
    $ids = $_POST['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'No users selected']);
        exit();
    }

    foreach ($ids as $id) {
        $stmt = $conn->prepare("DELETE FROM user_info WHERE userID = :id");
        $stmt->execute([':id' => $id]);

        logAdminAction($adminID, "Deleted User", $id, 'student');
    }

    echo json_encode(['status' => 'success', 'message' => 'Users deleted successfully']);
}

// Admin Action Logging supporting the audit_logs
function logAdminAction($adminID, $action, $userID, $role) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO audit_logs (admin_id, action, user_id, role) VALUES (:admin_id, :action, :user_id, :role)");
    $stmt->execute([':admin_id' => $adminID, ':action' => $action, ':user_id' => $userID, ':role' => $role]);
}
?>
