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

if ($result->num_rows <= 1) {
    // Check if we already seeded the 4 dummy ushers
    $check_ushers = $conn->query("SELECT id FROM users WHERE email LIKE 'dummy_usher_%@wasla.com'");
    $dummy_usher_ids = [];
    
    if ($check_ushers && $check_ushers->num_rows === 0) {
        // Insert 4 dummy ushers
        $dummy_ushers = [
            [801, 'Omar', 'Hassan', 'dummy_usher_1@wasla.com'],
            [802, 'Sara', 'Ahmed', 'dummy_usher_2@wasla.com'],
            [803, 'Khalid', 'Al-Farsi', 'dummy_usher_3@wasla.com'],
            [804, 'Fatima', 'Zahra', 'dummy_usher_4@wasla.com']
        ];
        
        foreach ($dummy_ushers as $du) {
            $istmt = $conn->prepare("INSERT IGNORE INTO users (id, first_name, last_name, email, role) VALUES (?, ?, ?, ?, 'usher')");
            $istmt->bind_param("isss", $du[0], $du[1], $du[2], $du[3]);
            $istmt->execute();
            $istmt->close();
            $dummy_usher_ids[] = $du[0];
        }
    } else {
        while ($row = $check_ushers->fetch_assoc()) {
            $dummy_usher_ids[] = $row['id'];
        }
    }

    // Insert welcome message from admin if not exists
    $admin_res = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if ($admin_res && $admin_res->num_rows > 0) {
        $admin_id = $admin_res->fetch_assoc()['id'];
    } else {
        $admin_id = 999;
        $conn->query("INSERT IGNORE INTO users (id, first_name, last_name, role) VALUES (999, 'Wasla', 'Support', 'admin')");
    }

    // Check if admin message exists
    $admin_msg_check = $conn->prepare("SELECT id FROM messages WHERE sender_id = ? AND receiver_id = ?");
    $admin_msg_check->bind_param("ii", $admin_id, $user_id);
    $admin_msg_check->execute();
    if ($admin_msg_check->get_result()->num_rows === 0) {
        $welcome_msg = "Welcome to Wasla! If you have any questions or need help, feel free to ask here.";
        $insert_stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES (?, ?, ?, 0)");
        $insert_stmt->bind_param("iis", $admin_id, $user_id, $welcome_msg);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    $admin_msg_check->close();

    // Insert dummy messages from the 4 ushers
    $dummy_messages = [
        "Sure, I'll be there at 9 AM",
        "The venue looks great! Do we have a dress code?",
        "Can we schedule a brief meeting before the event?",
        "Thanks for the update! I have received the schedule."
    ];
    
    foreach ($dummy_usher_ids as $index => $u_id) {
        // Only insert if no messages exist between them
        $mcheck = $conn->prepare("SELECT id FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
        $mcheck->bind_param("iiii", $u_id, $user_id, $user_id, $u_id);
        $mcheck->execute();
        if ($mcheck->get_result()->num_rows === 0) {
            $msg = $dummy_messages[$index % count($dummy_messages)];
            $istmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, is_read, sent_at) VALUES (?, ?, ?, 0, DATE_SUB(NOW(), INTERVAL ? MINUTE))");
            $minutes_ago = ($index + 1) * 15; // stagger the times
            $istmt->bind_param("iisi", $u_id, $user_id, $msg, $minutes_ago);
            $istmt->execute();
            $istmt->close();
        }
        $mcheck->close();
    }

    // Re-execute to get all the newly seeded conversations
    $stmt->execute();
    $result = $stmt->get_result();
}

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
