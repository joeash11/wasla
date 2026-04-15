<?php
// ============================================
// Get Messages between two Users
// Returns messages for a conversation
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'connection.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$partner_id = isset($_GET['partner_id']) ? intval($_GET['partner_id']) : 0;

if ($user_id <= 0 || $partner_id <= 0) {
    echo json_encode(['error' => 'Invalid user_id or partner_id']);
    exit;
}

// Mark messages from partner as read
$update_sql = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ii", $partner_id, $user_id);
$update_stmt->execute();
$update_stmt->close();

// Fetch messages
$sql = "SELECT 
    m.id,
    m.sender_id,
    m.receiver_id,
    m.message,
    m.is_read,
    m.sent_at,
    u.first_name AS sender_name
FROM messages m
JOIN users u ON u.id = m.sender_id
WHERE (m.sender_id = ? AND m.receiver_id = ?) 
   OR (m.sender_id = ? AND m.receiver_id = ?)
ORDER BY m.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $partner_id, $partner_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => $row['message'],
        'is_read' => $row['is_read'],
        'sent_at' => $row['sent_at'],
        'sender_name' => $row['sender_name'],
        'is_mine' => ($row['sender_id'] == $user_id)
    ];
}

echo json_encode($messages);
$stmt->close();
$conn->close();
?>
