<?php
// ============================================
// My Gigs API
// Returns usher's gig history (upcoming, completed, cancelled)
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$usher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['usher_id']) ? intval($_GET['usher_id']) : null);
if (!$usher_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// ---- Earnings Summary ----
// This month earnings
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
    WHERE payee_id = ? AND type = 'payout' AND status = 'completed'
    AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$this_month = floatval($stmt->get_result()->fetch_assoc()['total']);
$stmt->close();

// Last month earnings for comparison
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(amount), 0) as total FROM transactions 
    WHERE payee_id = ? AND type = 'payout' AND status = 'completed'
    AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
    AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$last_month = floatval($stmt->get_result()->fetch_assoc()['total']);
$stmt->close();

$month_change = $last_month > 0 ? round((($this_month - $last_month) / $last_month) * 100) : 0;

// Gigs this month count
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM project_applications pa
    JOIN projects p ON pa.project_id = p.id
    WHERE pa.usher_id = ? AND pa.status IN ('accepted','completed')
    AND MONTH(p.event_date) = MONTH(CURDATE()) AND YEAR(p.event_date) = YEAR(CURDATE())
");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$gigs_this_month = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

// Avg rating
$stmt = $conn->prepare("SELECT COALESCE(AVG(rating), 0) as avg_r FROM reviews WHERE usher_id = ?");
$stmt->bind_param("i", $usher_id);
$stmt->execute();
$avg_rating = round(floatval($stmt->get_result()->fetch_assoc()['avg_r']), 1);
$stmt->close();

// ---- All Gigs ----
$stmt = $conn->prepare("
    SELECT pa.status as app_status, pa.applied_at,
           p.id as project_id, p.title, p.event_date, p.end_date, p.location, p.city,
           p.pay_per_usher, p.status as project_status,
           u.company_name, u.first_name as client_first,
           DATE_FORMAT(p.event_date, '%d') as day_num,
           DATE_FORMAT(p.event_date, '%b') as month_abbr,
           DATE_FORMAT(p.event_date, '%h:%i %p') as start_time,
           (SELECT AVG(r.rating) FROM reviews r WHERE r.usher_id = ? AND r.project_id = p.id) as gig_rating
    FROM project_applications pa
    JOIN projects p ON pa.project_id = p.id
    JOIN users u ON p.client_id = u.id
    WHERE pa.usher_id = ?
    ORDER BY p.event_date DESC
");
$stmt->bind_param("ii", $usher_id, $usher_id);
$stmt->execute();
$result = $stmt->get_result();
$gigs = [];
while ($row = $result->fetch_assoc()) {
    $gig_status = 'upcoming';
    if ($row['app_status'] === 'completed') $gig_status = 'completed';
    elseif ($row['app_status'] === 'rejected' || $row['project_status'] === 'cancelled') $gig_status = 'cancelled';
    
    $gigs[] = [
        'id' => $row['project_id'],
        'title' => $row['title'],
        'company' => $row['company_name'] ?: $row['client_first'],
        'location' => $row['city'] . ', KSA',
        'day' => $row['day_num'],
        'month' => $row['month_abbr'],
        'pay' => floatval($row['pay_per_usher']),
        'status' => $gig_status,
        'rating' => $row['gig_rating'] ? round(floatval($row['gig_rating']), 1) : null,
        'hours' => 8
    ];
}
$stmt->close();

echo json_encode([
    'success' => true,
    'summary' => [
        'this_month_earnings' => $this_month,
        'month_change_pct' => $month_change,
        'gigs_this_month' => $gigs_this_month,
        'avg_rating' => $avg_rating
    ],
    'gigs' => $gigs
], JSON_PRETTY_PRINT);

$conn->close();
?>
