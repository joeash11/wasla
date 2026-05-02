<?php
// ============================================
// Admin Stats API — Returns platform-wide analytics
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

// Verify admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stats = [];

// Total users
$r = $conn->query("SELECT COUNT(*) as cnt FROM users");
$stats['total_users'] = $r->fetch_assoc()['cnt'];

// Users by role
$r = $conn->query("SELECT role, COUNT(*) as cnt FROM users GROUP BY role");
while ($row = $r->fetch_assoc()) {
    $stats[$row['role'] . 's'] = (int)$row['cnt'];
}

// Active projects
$r = $conn->query("SELECT COUNT(*) as cnt FROM projects WHERE status = 'active'");
$stats['active_projects'] = $r->fetch_assoc()['cnt'];

// Total revenue
$r = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE status = 'completed'");
$stats['total_revenue'] = (int)$r->fetch_assoc()['total'];

// New signups (last 30 days)
$r = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$stats['new_signups'] = $r->fetch_assoc()['cnt'];

// Monthly user growth (last 6 months)
$growth_labels = [];
$growth_data = [];
for ($i = 5; $i >= 0; $i--) {
    $monthStart = date('Y-m-01', strtotime("-{$i} months"));
    $monthEnd   = date('Y-m-t', strtotime("-{$i} months"));
    $label      = date('M Y', strtotime("-{$i} months"));
    
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM users WHERE created_at BETWEEN ? AND ?");
    $stmt->bind_param("ss", $monthStart, $monthEnd);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $growth_labels[] = $label;
    $growth_data[]   = (int)$result['cnt'];
    $stmt->close();
}

$stats['growth_labels'] = $growth_labels;
$stats['growth_data']   = $growth_data;

echo json_encode($stats);
$conn->close();
