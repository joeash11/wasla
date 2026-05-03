<?php
// ============================================
// Apply to Job API
// POST: project_id → creates application
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$usher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$usher_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$project_id = intval($input['project_id'] ?? 0);

if (!$project_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing project_id']);
    exit;
}

// Check if user has a CV uploaded
$stmt = $conn->prepare("SELECT cv_path FROM users WHERE id = ?");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Proceed without strict CV requirement for now
// if (empty($user_data['cv_path'])) {
//     echo json_encode(['success' => false, 'error' => 'You must upload your CV in Settings before applying.']);
//     exit;
// }

// Check if already applied
$stmt = $conn->prepare("SELECT id FROM project_applications WHERE project_id = ? AND usher_id = ?");
$stmt->bind_param("ii", $project_id, $usher_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'error' => 'Already applied']);
    exit;
}
$stmt->close();

// Create application
$stmt = $conn->prepare("INSERT INTO project_applications (project_id, usher_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("ii", $project_id, $usher_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Application submitted']);
$conn->close();
?>
