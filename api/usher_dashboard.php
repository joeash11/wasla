<?php
// ============================================
// Usher Dashboard API
// Returns JSON data for the usher dashboard
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

session_start();
require_once __DIR__ . '/../db/connection.php';

// Get usher ID from session or query param
$usher_id = isset($_GET['usher_id']) ? intval($_GET['usher_id']) : null;
if (!$usher_id && isset($_SESSION['user_id'])) {
    $usher_id = $_SESSION['user_id'];
}

if (!$usher_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing usher_id']);
    exit;
}

// Verify user exists and is an usher
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, city, skills, rating, created_at FROM users WHERE id = ? AND role = 'usher'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Usher not found']);
    exit;
}

// ---- STATS ----
// Completed gigs
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM project_applications WHERE usher_id = ? AND status = 'completed'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$completed_gigs = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Total earnings
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE payee_id = ? AND type = 'payout' AND status = 'completed'");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$total_earnings = floatval($stmt->get_result()->fetch_assoc()['total']);
$stmt->close();

// Average rating
$stmt = $conn->prepare("SELECT COALESCE(AVG(rating), 0) as avg_rating FROM reviews WHERE usher_id = ?");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$avg_rating = round(floatval($stmt->get_result()->fetch_assoc()['avg_rating']), 1);
$stmt->close();

// Upcoming shifts
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM projects WHERE status IN ('active', 'pending')
");
$stmt->execute();
$upcoming_shifts = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// ---- MONTHLY EARNINGS (last 6 months) ----
$stmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month_key,
        DATE_FORMAT(created_at, '%b') as month_label,
        SUM(amount) as total
    FROM transactions 
    WHERE payee_id = ? AND type = 'payout' AND status = 'completed'
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month_key, month_label
    ORDER BY month_key ASC
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$earnings_result = $stmt->get_result();
$monthly_earnings = [];
$max_earning = 0;
while ($row = $earnings_result->fetch_assoc()) {
    $amount = floatval($row['total']);
    if ($amount > $max_earning) $max_earning = $amount;
    $monthly_earnings[] = [
        'month' => $row['month_label'],
        'amount' => $amount
    ];
}
$stmt->close();

// Calculate percentage heights for bar chart
foreach ($monthly_earnings as &$m) {
    $m['percentage'] = $max_earning > 0 ? round(($m['amount'] / $max_earning) * 100) : 0;
}
unset($m);

// ---- UPCOMING GIGS ----
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.event_date, p.location, p.city, p.pay_per_usher,
           DATE_FORMAT(p.event_date, '%d') as day_num,
           DATE_FORMAT(p.event_date, '%b') as month_abbr
    FROM project_applications pa 
    JOIN projects p ON pa.project_id = p.id 
    WHERE pa.usher_id = ? AND pa.status = 'accepted' AND p.event_date >= CURDATE()
    ORDER BY p.event_date ASC
    LIMIT 5
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$upcoming_result = $stmt->get_result();
$upcoming_gigs = [];
while ($row = $upcoming_result->fetch_assoc()) {
    $upcoming_gigs[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'day' => $row['day_num'],
        'month' => $row['month_abbr'],
        'location' => $row['city'] . ', KSA',
        'pay' => floatval($row['pay_per_usher'])
    ];
}
$stmt->close();

// ---- RECENT ACTIVITY (union of events) ----
$stmt = $conn->prepare("
    (
        SELECT 'gig_completed' as type, p.title as detail, NULL as amount, 
               pa.responded_at as event_time
        FROM project_applications pa 
        JOIN projects p ON pa.project_id = p.id
        WHERE pa.usher_id = ? AND pa.status = 'completed'
    )
    UNION ALL
    (
        SELECT 'payment' as type, t.description as detail, t.amount,
               t.created_at as event_time
        FROM transactions t
        WHERE t.payee_id = ? AND t.type = 'payout' AND t.status = 'completed'
    )
    UNION ALL
    (
        SELECT 'review' as type, 
               CONCAT(u.first_name, ' ', u.last_name) as detail,
               r.rating as amount,
               r.created_at as event_time
        FROM reviews r
        JOIN users u ON r.reviewer_id = u.id
        WHERE r.usher_id = ?
    )
    ORDER BY event_time DESC
    LIMIT 5
");
$stmt->bind_param("iii", $usher_id, $usher_id, $usher_id);
$stmt->execute();
$activity_result = $stmt->get_result();
$recent_activity = [];
while ($row = $activity_result->fetch_assoc()) {
    $time_diff = time() - strtotime($row['event_time']);
    $time_ago = '';
    if ($time_diff < 3600) $time_ago = round($time_diff / 60) . ' min ago';
    elseif ($time_diff < 86400) $time_ago = round($time_diff / 3600) . ' hours ago';
    elseif ($time_diff < 604800) $time_ago = round($time_diff / 86400) . ' days ago';
    else $time_ago = round($time_diff / 604800) . ' weeks ago';

    $recent_activity[] = [
        'type' => $row['type'],
        'detail' => $row['detail'],
        'amount' => $row['amount'],
        'time_ago' => $time_ago
    ];
}
$stmt->close();

// ---- BUILD RESPONSE ----
$response = [
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'city' => $user['city'],
        'skills' => $user['skills'],
        'rating' => floatval($user['rating'])
    ],
    'stats' => [
        'completed_gigs' => $completed_gigs,
        'total_earnings' => $total_earnings,
        'avg_rating' => $avg_rating,
        'upcoming_shifts' => $upcoming_shifts
    ],
    'monthly_earnings' => $monthly_earnings,
    'upcoming_gigs' => $upcoming_gigs,
    'recent_activity' => $recent_activity
];

echo json_encode($response, JSON_PRETTY_PRINT);

$conn->close();
?>
