<?php
// ============================================
// Available Jobs API
// Returns all active/hiring projects as JSON
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$usher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['usher_id']) ? intval($_GET['usher_id']) : null);

// Get all active projects with application counts
$query = "
    SELECT p.id, p.title, p.description, p.event_date, p.end_date, p.location, p.city,
           p.ushers_needed, p.pay_per_usher, p.status, p.category,
           u.first_name as client_first, u.last_name as client_last, u.company_name,
           DATE_FORMAT(p.event_date, '%b %d, %Y') as formatted_date,
           (SELECT COUNT(*) FROM project_applications WHERE project_id = p.id AND status IN ('accepted','completed')) as ushers_filled
    FROM projects p
    JOIN users u ON p.client_id = u.id
    WHERE p.status IN ('active', 'pending')
    ORDER BY p.event_date ASC
";

$result = $conn->query($query);
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $slots_left = $row['ushers_needed'] - $row['ushers_filled'];
    
    // Check if current usher already applied
    $already_applied = false;
    if ($usher_id) {
        $stmt = $conn->prepare("SELECT status FROM project_applications WHERE project_id = ? AND usher_id = ?");
        $stmt->bind_param("ii", $row['id'], $usher_id);
        $stmt->execute();
        $app = $stmt->get_result()->fetch_assoc();
        if ($app) $already_applied = $app['status'];
        $stmt->close();
    }
    
    $jobs[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'company' => $row['company_name'] ?: ($row['client_first'] . ' ' . $row['client_last']),
        'location' => $row['city'] . ', KSA',
        'date' => $row['formatted_date'],
        'event_date' => $row['event_date'],
        'hours' => 8,
        'pay' => floatval($row['pay_per_usher']),
        'status' => $row['status'],
        'category' => $row['category'],
        'slots_left' => max(0, $slots_left),
        'ushers_needed' => $row['ushers_needed'],
        'already_applied' => $already_applied
    ];
}

echo json_encode(['success' => true, 'jobs' => $jobs], JSON_PRETTY_PRINT);
$conn->close();
?>
