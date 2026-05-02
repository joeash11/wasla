<?php
// ============================================
// Login Handler - With Email Verification Code
// ============================================
session_start();
require_once __DIR__ . '/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'client';

// Validate inputs
if (empty($email) || empty($password)) {
    header('Location: login.php?error=empty');
    exit;
}

// Look up user by email
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password, role, is_active FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: login.php?error=invalid');
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($password, $user['password'])) {
    header('Location: login.php?error=invalid');
    exit;
}

// Check if account is active
if (!$user['is_active']) {
    header('Location: login.php?error=inactive');
    exit;
}

// For non-admins, ensure they selected the correct tab
if ($user['role'] !== 'admin' && $user['role'] !== $role) {
    header('Location: login.php?error=wrong_privilege');
    exit;
}

// Log the user in immediately for a smooth, fast login experience
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role']  = $user['role'];
$_SESSION['logged_in']  = true;

// Redirect based on role
switch ($user['role']) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'usher':
        header('Location: usher/dashboard.php');
        break;
    case 'client':
    default:
        header('Location: dashboard.php');
        break;
}
exit;
?>
