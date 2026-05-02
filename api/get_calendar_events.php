<?php
// ============================================
// Get Calendar Events for the logged-in user
// Returns accepted/active projects as calendar events
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? 'client';
$events = [];

if ($role === 'client') {
    // Client sees their own projects
    $stmt = $conn->prepare("SELECT id, title, event_date, end_date, location, status FROM projects WHERE client_id = ? AND status IN ('active','pending') ORDER BY event_date ASC");
    $stmt->bind_param("i", $user_id);
} else {
    // Usher sees projects they've been accepted to
    $stmt = $conn->prepare("SELECT p.id, p.title, p.event_date, p.end_date, p.location, p.status FROM projects p JOIN project_applications pa ON pa.project_id = p.id WHERE pa.usher_id = ? AND pa.status IN ('accepted','completed') ORDER BY p.event_date ASC");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$colors = [
    'active'    => '#00c9a7',
    'pending'   => '#f59e0b',
    'completed' => '#6366f1'
];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => $row['title'],
        'start' => $row['event_date'],
        'end'   => $row['end_date'] ? date('Y-m-d', strtotime($row['end_date'] . ' +1 day')) : null,
        'color' => $colors[$row['status']] ?? '#00c9a7',
        'extendedProps' => [
            'location' => $row['location'],
            'status'   => $row['status']
        ]
    ];
}

$stmt->close();
$conn->close();
echo json_encode($events);
