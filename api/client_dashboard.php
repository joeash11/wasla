<?php
// ============================================
// Client Dashboard API
// Returns real stats for the logged-in client
// ============================================
session_start();
header('Content-Type: application/json');

// Check auth
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['logged_in' => false, 'error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../db/connection.php';

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Return user info
$response = [
    'logged_in'   => true,
    'user_id'     => $user_id,
    'user_name'   => $_SESSION['user_name'] ?? '',
    'first_name'  => $_SESSION['first_name'] ?? '',
    'user_email'  => $_SESSION['user_email'] ?? '',
    'user_role'   => $user_role,
];

if ($user_role === 'client') {
    // Active projects count
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM projects WHERE client_id = ? AND status = 'active'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $response['active_projects'] = $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();

    // Total ushers hired (accepted or completed applications on client's projects)
    $stmt = $conn->prepare(
        "SELECT COUNT(DISTINCT pa.usher_id) as cnt 
         FROM project_applications pa 
         JOIN projects p ON pa.project_id = p.id 
         WHERE p.client_id = ? AND pa.status IN ('accepted', 'completed')"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $response['total_ushers_hired'] = $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();

    // Upcoming events (projects with event_date >= today)
    $stmt = $conn->prepare(
        "SELECT COUNT(*) as cnt FROM projects 
         WHERE client_id = ? AND status IN ('active', 'pending') AND event_date >= CURDATE()"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $response['upcoming_events'] = $stmt->get_result()->fetch_assoc()['cnt'];
    $stmt->close();

    // Total revenue (completed payments received)
    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
         WHERE payee_id = ? AND status = 'completed'"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $response['total_revenue'] = floatval($stmt->get_result()->fetch_assoc()['total']);
    $stmt->close();

    // Pending payments
    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
         WHERE (payer_id = ? OR payee_id = ?) AND status = 'pending'"
    );
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $response['pending_payments'] = floatval($stmt->get_result()->fetch_assoc()['total']);
    $stmt->close();

    // Total spent (completed payments made by client)
    $stmt = $conn->prepare(
        "SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
         WHERE payer_id = ? AND status = 'completed'"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $response['total_spent'] = floatval($stmt->get_result()->fetch_assoc()['total']);
    $stmt->close();

    // Recent transactions
    $stmt = $conn->prepare(
        "SELECT t.amount, t.type, t.status, t.description, t.created_at,
                CONCAT(u.first_name, ' ', u.last_name) as other_party
         FROM transactions t
         LEFT JOIN users u ON (CASE WHEN t.payer_id = ? THEN t.payee_id ELSE t.payer_id END) = u.id
         WHERE t.payer_id = ? OR t.payee_id = ?
         ORDER BY t.created_at DESC
         LIMIT 5"
    );
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $row['is_expense'] = true; // client perspective: payments are expenses
        $transactions[] = $row;
    }
    $response['recent_transactions'] = $transactions;
    $stmt->close();

    // Client's projects for the project cards
    $stmt = $conn->prepare(
        "SELECT p.id, p.title, p.event_date, p.end_date, p.location, p.city, 
                p.ushers_needed, p.status, p.category, p.image,
                (SELECT COUNT(*) FROM project_applications pa WHERE pa.project_id = p.id AND pa.status IN ('accepted','completed')) as ushers_hired
         FROM projects p
         WHERE p.client_id = ?
         ORDER BY p.event_date DESC"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $row['ushers_remaining'] = max(0, $row['ushers_needed'] - $row['ushers_hired']);
        $projects[] = $row;
    }
    $response['projects'] = $projects;
    $stmt->close();

} elseif ($user_role === 'usher') {
    // For ushers, redirect them to usher dashboard data
    $response['redirect'] = 'usher/dashboard.php';
}

echo json_encode($response);
$conn->close();
?>
