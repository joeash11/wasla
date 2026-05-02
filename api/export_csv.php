<?php
// ============================================
// Export CSV — Admin-only data export
// Supports: users, projects, transactions, reports
// ============================================
session_start();
require_once __DIR__ . '/../db/connection.php';

// Verify admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized');
}

$type = $_GET['type'] ?? 'users';
$filename = 'wasla_' . $type . '_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

switch ($type) {
    case 'users':
        fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Company', 'City', 'Category', 'Rating', 'Verified', 'Active', 'Created At']);
        $result = $conn->query("SELECT id, first_name, last_name, email, phone, role, company_name, city, category, rating, is_verified, is_active, created_at FROM users ORDER BY id");
        while ($row = $result->fetch_assoc()) {
            $row['is_verified'] = $row['is_verified'] ? 'Yes' : 'No';
            $row['is_active'] = $row['is_active'] ? 'Yes' : 'No';
            fputcsv($output, $row);
        }
        break;

    case 'projects':
        fputcsv($output, ['ID', 'Title', 'Client ID', 'Event Date', 'End Date', 'Location', 'City', 'Ushers Needed', 'Pay Per Usher (EGP)', 'Status', 'Category', 'Created At']);
        $result = $conn->query("SELECT id, title, client_id, event_date, end_date, location, city, ushers_needed, pay_per_usher, status, category, created_at FROM projects ORDER BY id");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'transactions':
        fputcsv($output, ['ID', 'Payer ID', 'Payee ID', 'Project ID', 'Amount (EGP)', 'Type', 'Status', 'Description', 'Created At']);
        $result = $conn->query("SELECT id, payer_id, payee_id, project_id, amount, type, status, description, created_at FROM transactions ORDER BY id");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    case 'reports':
        fputcsv($output, ['ID', 'Reporter ID', 'Reported ID', 'Project ID', 'Reporter Role', 'Reason', 'Description', 'Status', 'Admin Notes', 'Created At']);
        $result = $conn->query("SELECT id, reporter_id, reported_id, project_id, reporter_role, reason, description, status, admin_notes, created_at FROM reports ORDER BY id");
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        break;

    default:
        fputcsv($output, ['Error']);
        fputcsv($output, ['Unknown export type: ' . $type]);
}

fclose($output);
$conn->close();
