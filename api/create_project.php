<?php
// ============================================
// Create Project API
// Creates a new project with 'pending' status
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
    echo json_encode(['success' => false, 'error' => 'Only clients can create projects']);
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
$required = ['title', 'event_date', 'location', 'city', 'ushers_needed'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit;
    }
}

$client_id    = $_SESSION['user_id'];
$title        = trim($data['title']);
$description  = trim($data['description'] ?? '');
$event_date   = $data['event_date'];
$end_date     = $data['end_date'] ?? null;
$location     = trim($data['location']);
$city         = trim($data['city']);
$ushers_needed = intval($data['ushers_needed']);
$pay_per_usher = isset($data['pay_per_usher']) ? floatval($data['pay_per_usher']) : null;
$category     = trim($data['category'] ?? '');

// Projects start as PENDING until an admin approves them
$status = 'pending';

$stmt = $conn->prepare(
    "INSERT INTO projects (client_id, title, description, event_date, end_date, location, city, ushers_needed, pay_per_usher, status, category)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issssssidss",
    $client_id, $title, $description, $event_date, $end_date,
    $location, $city, $ushers_needed, $pay_per_usher, $status, $category
);

if ($stmt->execute()) {
    $project_id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Project created successfully! It will be reviewed by an admin before going live.',
        'project_id' => $project_id,
        'status' => 'pending'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to create project']);
}

$stmt->close();
$conn->close();
?>
