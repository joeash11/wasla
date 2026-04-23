<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

if (!$client_id || !$project_id) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

// Verify this project belongs to this client
$stmt = $conn->prepare("SELECT id FROM projects WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $project_id, $client_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized or project not found']);
    exit;
}
$stmt->close();

// Fetch applicants
$stmt = $conn->prepare("
    SELECT pa.id as application_id, pa.status, u.id as usher_id, u.first_name, u.last_name, u.rating, u.cv_path 
    FROM project_applications pa
    JOIN users u ON pa.usher_id = u.id
    WHERE pa.project_id = ? AND pa.status = 'pending'
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res = $stmt->get_result();
$applicants = [];
while ($row = $res->fetch_assoc()) {
    $applicants[] = [
        'application_id' => $row['application_id'],
        'usher_id' => $row['usher_id'],
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'rating' => $row['rating'] ? round((float)$row['rating'], 1) : null,
        'cv_url' => $row['cv_path'] ? '/' . ltrim($row['cv_path'], '/') : null
    ];
}
$stmt->close();

echo json_encode(['success' => true, 'applicants' => $applicants]);
$conn->close();
?>
