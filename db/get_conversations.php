<?php
// ============================================
// Get Conversations for a User
// Returns list of conversation partners with last message
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'connection.php';

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['error' => 'Invalid user_id']);
    exit;
}

$sql = "SELECT 
    u.id AS partner_id,
    u.first_name,
    u.last_name,
    u.profile_image,
    m.message AS last_message,
    m.sent_at AS last_time,
    m.is_read,
    (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) AS unread_count
FROM users u
INNER JOIN (
    SELECT 
        CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS partner_id,
        MAX(id) AS max_id
    FROM messages
    WHERE sender_id = ? OR receiver_id = ?
    GROUP BY partner_id
) latest ON u.id = latest.partner_id
INNER JOIN messages m ON m.id = latest.max_id
ORDER BY m.sent_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
    $conversations[] = [
        'partner_id' => $row['partner_id'],
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'profile_image' => $row['profile_image'],
        'last_message' => $row['last_message'],
        'last_time' => $row['last_time'],
        'is_read' => $row['is_read'],
        'unread_count' => $row['unread_count']
    ];
}

echo json_encode($conversations);
$stmt->close();
$conn->close();
?>
