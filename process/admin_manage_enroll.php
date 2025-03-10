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
s
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access']));
}

$superAdminID = $_SESSION['admin_id'];  // Get logged-in Superadmin ID

// Approve, Suspend, or Change Role
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_id'], $_POST['new_status'], $_POST['new_role'])) {
    $targetAdminID = trim($_POST['admin_id']);
    $newStatus = trim($_POST['new_status']);  // 'active', 'suspended'
    $newRole = trim($_POST['new_role']);  // 'admin', 'superadmin'
    
    if (!in_array($newStatus, ['active', 'suspended'], true)) {
        http_response_code(400);
        die(json_encode(['status' => 'error', 'message' => 'Invalid status']));
    }

    if (!in_array($newRole, ['admin', 'superadmin'], true)) {
        http_response_code(400);
        die(json_encode(['status' => 'error', 'message' => 'Invalid role']));
    }

    // Prevent Modification of Superadmins by Other Admins
    $stmt = $conn->prepare("SELECT admin_role FROM admin_info WHERE adminID = :adminID");
    $stmt->execute([':adminID' => $targetAdminID]);
    $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminData) {
        http_response_code(404);
        die(json_encode(['status' => 'error', 'message' => 'Admin not found']));
    }

    if ($adminData['admin_role'] === 'superadmin' && $_SESSION['admin_id'] !== $targetAdminID) {
        http_response_code(403);
        die(json_encode(['status' => 'error', 'message' => 'You cannot modify another superadmin']));
    }

    $stmt = $conn->prepare("UPDATE admin_info SET admin_status = :new_status, admin_role = :new_role WHERE adminID = :adminID");
    $stmt->execute([':new_status' => $newStatus, ':new_role' => $newRole, ':adminID' => $targetAdminID]);

    logAdminAction($superAdminID, "Updated Admin Role/Status", $targetAdminID, "admin", "New Role: $newRole, New Status: $newStatus");

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Admin role and status updated']);
}
?>
