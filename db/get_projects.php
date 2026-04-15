<?php
// ============================================
// Get Projects API
// GET: ?user_id=X&status=active|completed|pending
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'connection.php';
require_once 'session.php';

$user_id = null;
if (isLoggedIn()) {
    $user_id = getCurrentUserId();
} elseif (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
}

if (!$user_id) $user_id = 2;

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT p.*, 
    (SELECT COUNT(*) FROM project_applications WHERE project_id = p.id AND status = 'accepted') as hired_ushers,
    (SELECT COUNT(*) FROM project_applications WHERE project_id = p.id AND status = 'pending') as pending_ushers
    FROM projects p WHERE p.client_id = ?";
$params = [$user_id];
$types = "i";

if (!empty($status) && $status !== 'all') {
    $sql .= " AND p.status = ?";
    $params[] = $status;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND p.title LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    // Calculate remaining ushers
    $row['ushers_remaining'] = max(0, $row['ushers_needed'] - $row['hired_ushers']);
    $projects[] = $row;
}

echo json_encode($projects);

$stmt->close();
$conn->close();
?>
