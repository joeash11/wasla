<?php
// ============================================
// Check Notifications — Returns unread count
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'] ?? 'client';
$count = 0;

if ($role === 'client') {
    // Count pending applications for the client's projects
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM project_applications pa JOIN projects p ON pa.project_id = p.id WHERE p.client_id = ? AND pa.status = 'pending'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = (int)$result['cnt'];
    $stmt->close();
} else {
    // Count accepted applications for the usher
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM project_applications WHERE usher_id = ? AND status = 'accepted' AND responded_at > DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $count = (int)$result['cnt'];
    $stmt->close();
}

echo json_encode(['count' => $count]);
$conn->close();
