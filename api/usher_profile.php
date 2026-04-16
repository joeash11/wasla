<?php
// ============================================
// Usher Profile API
// Returns full usher profile data
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$usher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['usher_id']) ? intval($_GET['usher_id']) : null);
if (!$usher_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// User info
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone, city, skills, bio, rating, created_at FROM users WHERE id = ? AND role = 'usher'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Usher not found']);
    exit;
}

// Stats
$stmt = $conn->prepare("SELECT COUNT(*) as c FROM project_applications WHERE usher_id = ? AND status = 'completed'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$completed = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(AVG(rating),0) as r FROM reviews WHERE usher_id = ?");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$avg_rating = round(floatval($stmt->get_result()->fetch_assoc()['r']), 1);
$stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount),0) as t FROM transactions WHERE payee_id = ? AND type='payout' AND status='completed'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$total_earned = floatval($stmt->get_result()->fetch_assoc()['t']);
$stmt->close();

// Months active
$months_active = max(1, ceil((time() - strtotime($user['created_at'])) / (30 * 86400)));

// Reviews
$stmt = $conn->prepare("
    SELECT r.rating, r.comment, r.created_at,
           u.first_name, u.last_name,
           DATE_FORMAT(r.created_at, '%b %d, %Y') as formatted_date
    FROM reviews r
    JOIN users u ON r.reviewer_id = u.id
    WHERE r.usher_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = [];
while ($row = $reviews_result->fetch_assoc()) {
    $reviews[] = [
        'reviewer' => $row['first_name'] . ' ' . $row['last_name'],
        'rating' => intval($row['rating']),
        'comment' => $row['comment'],
        'date' => $row['formatted_date']
    ];
}
$stmt->close();

echo json_encode([
    'success' => true,
    'user' => [
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'city' => $user['city'],
        'skills' => $user['skills'] ? explode(', ', $user['skills']) : [],
        'bio' => $user['bio']
    ],
    'stats' => [
        'completed_gigs' => $completed,
        'avg_rating' => $avg_rating,
        'total_earned' => $total_earned,
        'months_active' => $months_active
    ],
    'reviews' => $reviews
], JSON_PRETTY_PRINT);

$conn->close();
?>
