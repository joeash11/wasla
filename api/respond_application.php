<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$input = json_decode(file_get_contents('php://input'), true);

if (!$client_id || !$input || !isset($input['application_id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$app_id = intval($input['application_id']);
$status = $input['status']; // 'accepted' or 'rejected'

if (!in_array($status, ['accepted', 'rejected'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

// Ensure the application exists and belongs to a project owned by this client
$stmt = $conn->prepare("
    SELECT pa.id, p.ushers_needed, 
           (SELECT COUNT(*) FROM project_applications WHERE project_id = p.id AND status = 'accepted') as accepted_count
    FROM project_applications pa
    JOIN projects p ON pa.project_id = p.id
    WHERE pa.id = ? AND p.client_id = ? AND pa.status = 'pending'
");
$stmt->bind_param("ii", $app_id, $client_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid application or unauthorized']);
    exit;
}

$row = $res->fetch_assoc();
$stmt->close();

if ($status === 'accepted' && $row['accepted_count'] >= $row['ushers_needed']) {
    echo json_encode(['success' => false, 'error' => 'Project has already reached the required number of ushers.']);
    exit;
}

$stmt = $conn->prepare("UPDATE project_applications SET status = ?, responded_at = CURRENT_TIMESTAMP WHERE id = ?");
$stmt->bind_param("si", $status, $app_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
