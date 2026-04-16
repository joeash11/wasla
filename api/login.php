<?php
// ============================================
// Login API Handler
// POST: email, password → session + redirect URL
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data (supports both form-encoded and JSON)
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? 'client';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

// Look up user by email
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role, is_active FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

// Verify password
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

// Check if account is active
if (!$user['is_active']) {
    http_response_code(403);
    echo json_encode(['error' => 'Account is suspended']);
    exit;
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['first_name'] = $user['first_name'];

// Determine redirect URL based on role
$redirect = '/wasla/dashboard.php';
switch ($user['role']) {
    case 'usher':
        $redirect = '/wasla/usher/dashboard.php';
        break;
    case 'admin':
        $redirect = '/wasla/admin/dashboard.php';
        break;
    case 'client':
    default:
        $redirect = '/wasla/dashboard.php';
        break;
}

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'role' => $user['role']
    ],
    'redirect' => $redirect
]);

$conn->close();
?>
