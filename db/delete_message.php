<?php
// ============================================
// Delete Individual Message API
// Deletes a specific message if the user is the sender
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$msg_id = isset($data['msg_id']) ? intval($data['msg_id']) : 0;
$user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;

if ($msg_id <= 0 || $user_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

// Only allow deleting if the user is the sender of this message
$stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
$stmt->bind_param("ii", $msg_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Message not found or unauthorized']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
