<?php
// ============================================
// Update Project API
// Updates an existing project
// Requires authenticated client session
// ============================================
session_start();
header('Content-Type: application/json');

// Check auth
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit;
}

if ($_SESSION['user_role'] !== 'client') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Only clients can edit projects']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../db/connection.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['project_id', 'title', 'event_date', 'location', 'city', 'ushers_needed'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

$client_id     = $_SESSION['user_id'];
$project_id    = intval($data['project_id']);
$title         = trim($data['title']);
$description   = trim($data['description'] ?? '');
$event_date    = $data['event_date'];
$end_date      = $data['end_date'] ?? null;
$location      = trim($data['location']);
$city          = trim($data['city']);
$ushers_needed = intval($data['ushers_needed']);
$pay_per_usher = isset($data['pay_per_usher']) && $data['pay_per_usher'] !== '' ? floatval($data['pay_per_usher']) : null;
$category      = trim($data['category'] ?? '');

// Check if project exists and belongs to client
$check_stmt = $conn->prepare("SELECT status FROM projects WHERE id = ? AND client_id = ?");
$check_stmt->bind_param("ii", $project_id, $client_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Project not found or unauthorized']);
    exit;
}
$project = $result->fetch_assoc();
$check_stmt->close();

if ($project['status'] === 'cancelled' || $project['status'] === 'completed') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Cannot edit cancelled or completed projects']);
    exit;
}

// Update the project. We don't change the status back to pending if they are just editing small details.
$stmt = $conn->prepare(
    "UPDATE projects 
     SET title = ?, description = ?, event_date = ?, end_date = ?, location = ?, city = ?, ushers_needed = ?, pay_per_usher = ?, category = ?
     WHERE id = ? AND client_id = ?"
);

$stmt->bind_param(
    "ssssssidssi",
    $title, $description, $event_date, $end_date,
    $location, $city, $ushers_needed, $pay_per_usher, $category,
    $project_id, $client_id
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Project updated successfully!'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update project']);
}

$stmt->close();
$conn->close();
?>
