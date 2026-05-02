<?php
// ============================================
// Submit Review — Client rates an Usher after project completion
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$reviewer_id = $_SESSION['user_id'];
$usher_id    = (int)($input['usher_id'] ?? 0);
$project_id  = (int)($input['project_id'] ?? 0);
$rating      = (int)($input['rating'] ?? 0);
$comment     = trim($input['comment'] ?? '');

// Validation
if ($usher_id <= 0 || $project_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'error' => 'Invalid rating data. Rating must be 1-5.']);
    exit;
}

// Verify the reviewer owns this project
$stmt = $conn->prepare("SELECT id FROM projects WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $project_id, $reviewer_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'You can only review ushers on your own projects.']);
    exit;
}
$stmt->close();

// Check for duplicate review
$stmt = $conn->prepare("SELECT id FROM reviews WHERE reviewer_id = ? AND usher_id = ? AND project_id = ?");
$stmt->bind_param("iii", $reviewer_id, $usher_id, $project_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'You have already reviewed this usher for this project.']);
    exit;
}
$stmt->close();

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (reviewer_id, usher_id, project_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiis", $reviewer_id, $usher_id, $project_id, $rating, $comment);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to save review.']);
    exit;
}
$stmt->close();

// Recalculate usher's average rating
$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE usher_id = ?");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$avg = $stmt->get_result()->fetch_assoc()['avg_rating'];
$stmt->close();

$newRating = round($avg, 2);
$stmt = $conn->prepare("UPDATE users SET rating = ? WHERE id = ?");
$stmt->bind_param("di", $newRating, $usher_id);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'new_rating' => $newRating, 'message' => 'Review submitted successfully!']);
$conn->close();
