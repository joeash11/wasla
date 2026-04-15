<?php
// ============================================
// Create Project API
// POST: { title, description, event_date, end_date, location, city, ushers_needed, budget, pay_per_usher, category }
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST method required']);
    exit;
}

require_once 'connection.php';
require_once 'session.php';

// Use session or default to user 2
$user_id = isLoggedIn() ? getCurrentUserId() : 2;

$data = json_decode(file_get_contents('php://input'), true);

$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$event_date = $data['event_date'] ?? '';
$end_date = $data['end_date'] ?? null;
$location = trim($data['location'] ?? '');
$city = trim($data['city'] ?? 'Cairo');
$ushers_needed = intval($data['ushers_needed'] ?? 1);
$budget = floatval($data['budget'] ?? 0);
$pay_per_usher = floatval($data['pay_per_usher'] ?? 0);
$category = trim($data['category'] ?? '');

if (empty($title) || empty($event_date) || empty($location)) {
    echo json_encode(['error' => 'Title, event date, and location are required']);
    exit;
}

$sql = "INSERT INTO projects (client_id, title, description, event_date, end_date, location, city, ushers_needed, budget, pay_per_usher, category, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssidds", $user_id, $title, $description, $event_date, $end_date, $location, $city, $ushers_needed, $budget, $pay_per_usher, $category);

if ($stmt->execute()) {
    $project_id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'project_id' => $project_id,
        'message' => 'Project created successfully'
    ]);
} else {
    echo json_encode(['error' => 'Failed to create project: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
