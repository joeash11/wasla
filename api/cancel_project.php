<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$project_id = isset($input['project_id']) ? intval($input['project_id']) : 0;

if (!$client_id || !$project_id) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Verify project belongs to user
$stmt = $conn->prepare("UPDATE projects SET status = 'cancelled' WHERE id = ? AND client_id = ? AND status != 'cancelled'");
$stmt->bind_param("ii", $project_id, $client_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Also cancel all pending/accepted applications
    $stmt2 = $conn->prepare("UPDATE project_applications SET status = 'rejected' WHERE project_id = ? AND status IN ('pending', 'accepted')");
    $stmt2->bind_param("i", $project_id);
    $stmt2->execute();
    $stmt2->close();
    
    echo json_encode(['success' => true, 'message' => 'Project cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Project not found, already cancelled, or unauthorized']);
}

$stmt->close();
$conn->close();
?>
