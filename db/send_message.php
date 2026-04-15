<?php
// ============================================
// Send a Message
// Inserts a new message into the database
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

$data = json_decode(file_get_contents('php://input'), true);

$sender_id = isset($data['sender_id']) ? intval($data['sender_id']) : 0;
$receiver_id = isset($data['receiver_id']) ? intval($data['receiver_id']) : 0;
$message = isset($data['message']) ? trim($data['message']) : '';

if ($sender_id <= 0 || $receiver_id <= 0 || empty($message)) {
    echo json_encode(['error' => 'Missing required fields: sender_id, receiver_id, message']);
    exit;
}

$sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    $msg_id = $stmt->insert_id;
    
    // Fetch the newly inserted message
    $fetch = $conn->prepare("SELECT id, sender_id, receiver_id, message, is_read, sent_at FROM messages WHERE id = ?");
    $fetch->bind_param("i", $msg_id);
    $fetch->execute();
    $result = $fetch->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => $result
    ]);
    $fetch->close();
} else {
    echo json_encode(['error' => 'Failed to send message: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
