<?php
// ============================================
// Dashboard Stats API
// GET: ?user_id=X
// Returns stats, revenue, transactions, projects
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'connection.php';
require_once 'session.php';

// Allow both session and query param
$user_id = null;
if (isLoggedIn()) {
    $user_id = getCurrentUserId();
} elseif (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
}

if (!$user_id) {
    // Default to user 2 (Abdullah) for demo
    $user_id = 2;
}

// Active projects count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM projects WHERE client_id = ? AND status = 'active'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$active_projects = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Total ushers hired
$stmt = $conn->prepare("SELECT COALESCE(SUM(pa.id IS NOT NULL), 0) as count FROM project_applications pa 
    JOIN projects p ON pa.project_id = p.id WHERE p.client_id = ? AND pa.status = 'accepted'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$ushers_hired = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Upcoming events
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM projects WHERE client_id = ? AND event_date >= CURDATE() AND status IN ('active','pending')");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcoming_events = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Total revenue (payments received)
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE payee_id = ? AND type = 'payment' AND status = 'completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_revenue = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Pending payments
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total, COUNT(*) as count FROM transactions WHERE payer_id = ? AND status = 'pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Total spent
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE payer_id = ? AND type = 'payment' AND status = 'completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_spent = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Recent transactions
$stmt = $conn->prepare("SELECT t.*, p.title as project_title FROM transactions t 
    LEFT JOIN projects p ON t.project_id = p.id 
    WHERE t.payer_id = ? OR t.payee_id = ? 
    ORDER BY t.created_at DESC LIMIT 5");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$tx_result = $stmt->get_result();
$transactions = [];
while ($row = $tx_result->fetch_assoc()) {
    $transactions[] = $row;
}
$stmt->close();

// User's projects for dashboard
$stmt = $conn->prepare("SELECT id, title, event_date, location, city, ushers_needed, status, image, category 
    FROM projects WHERE client_id = ? ORDER BY created_at DESC LIMIT 6");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$proj_result = $stmt->get_result();
$projects = [];
while ($row = $proj_result->fetch_assoc()) {
    $projects[] = $row;
}
$stmt->close();

echo json_encode([
    'stats' => [
        'active_projects' => intval($active_projects),
        'ushers_hired' => intval($ushers_hired),
        'upcoming_events' => intval($upcoming_events)
    ],
    'revenue' => [
        'total' => floatval($total_revenue),
        'pending' => floatval($pending['total']),
        'pending_count' => intval($pending['count']),
        'spent' => floatval($total_spent)
    ],
    'transactions' => $transactions,
    'projects' => $projects
]);

$conn->close();
?>
