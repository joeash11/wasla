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

// Fetch applicants with additional data for smart matching
$stmt = $conn->prepare("
    SELECT pa.id as application_id, pa.status, u.id as usher_id, u.first_name, u.last_name, u.rating, u.cv_path, u.skills,
           p.category as project_category,
           (SELECT COUNT(*) FROM project_applications pa2 WHERE pa2.usher_id = u.id AND pa2.status = 'completed') as completed_jobs
    FROM project_applications pa
    JOIN users u ON pa.usher_id = u.id
    JOIN projects p ON pa.project_id = p.id
    WHERE pa.project_id = ? AND pa.status = 'pending'
");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$res = $stmt->get_result();
$applicants = [];
while ($row = $res->fetch_assoc()) {
    // Smart Match Logic
    $score = 50; // Base score
    
    // Rating boost
    $rating = $row['rating'] ? (float)$row['rating'] : 0;
    if ($rating >= 4.5) $score += 20;
    else if ($rating >= 4.0) $score += 10;
    else if ($rating > 0 && $rating < 3.0) $score -= 10;
    
    // Experience boost
    $completed = (int)$row['completed_jobs'];
    if ($completed > 10) $score += 15;
    else if ($completed > 3) $score += 10;
    
    // Skill match boost
    $skills = strtolower($row['skills'] ?: '');
    $cat = strtolower($row['project_category'] ?: '');
    if ($cat && strpos($skills, $cat) !== false) {
        $score += 15;
    }
    
    // Cap at 99% (save 100% for perfect manual matches if needed)
    $score = min(99, max(10, $score));

    $applicants[] = [
        'application_id' => $row['application_id'],
        'usher_id' => $row['usher_id'],
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'rating' => $row['rating'] ? round((float)$row['rating'], 1) : null,
        'cv_url' => $row['cv_path'] ? '/' . ltrim($row['cv_path'], '/') : null,
        'match_score' => $score
    ];
}
$stmt->close();

// Sort applicants by match score descending
usort($applicants, function($a, $b) {
    return $b['match_score'] <=> $a['match_score'];
});

echo json_encode(['success' => true, 'applicants' => $applicants]);
$conn->close();
?>
