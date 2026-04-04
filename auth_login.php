<?php
// ============================================
// Login Handler - Processes login form
// ============================================
session_start();
require_once __DIR__ . '/db/connection.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'client';

// Validate inputs
if (empty($email) || empty($password)) {
    header('Location: login.html?error=empty');
    exit;
}

// Look up user by email
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role, is_active FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: login.html?error=invalid');
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($password, $user['password'])) {
    header('Location: login.html?error=invalid');
    exit;
}

// Check if account is active
if (!$user['is_active']) {
    header('Location: login.html?error=inactive');
    exit;
}

// Verify role matches what user selected
// Admin can only login through admin toggle
if ($role === 'admin' && $user['role'] !== 'admin') {
    header('Location: login.html?error=role');
    exit;
}

// For client/usher, check the role matches
if ($role !== 'admin' && $user['role'] !== $role && $user['role'] !== 'admin') {
    header('Location: login.html?error=role');
    exit;
}

// ---- Login Successful ----
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];
$_SESSION['logged_in']  = true;

// Redirect based on role
switch ($user['role']) {
    case 'admin':
        header('Location: admin/dashboard.html');
        break;
    case 'usher':
        header('Location: usher/dashboard.html');
        break;
    case 'client':
    default:
        header('Location: index.html');
        break;
}
exit;
?>
