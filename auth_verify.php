<?php
// ============================================
// Email Verification Handler
// Validates the 6-digit code entered by user
// ============================================
session_start();
require_once __DIR__ . '/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$entered_code = trim($_POST['code'] ?? '');

// Check if there's a pending verification
if (!isset($_SESSION['pending_user_id']) || !isset($_SESSION['pending_code'])) {
    header('Location: login.html?error=expired');
    exit;
}

// Check if code has expired (10 minutes)
if (time() - $_SESSION['pending_code_time'] > 600) {
    // Clear pending data
    unset($_SESSION['pending_user_id'], $_SESSION['pending_code'], $_SESSION['pending_code_time']);
    header('Location: login.html?error=expired');
    exit;
}

// Verify the code
if ($entered_code !== $_SESSION['pending_code']) {
    header('Location: verify-email.html?error=wrong_code');
    exit;
}

// Code is correct - complete login
$_SESSION['user_id']    = $_SESSION['pending_user_id'];
$_SESSION['user_name']  = $_SESSION['pending_user_name'];
$_SESSION['user_email'] = $_SESSION['pending_user_email'];
$_SESSION['user_role']  = $_SESSION['pending_user_role'];
$_SESSION['logged_in']  = true;

// Mark user as verified in DB
$stmt = $conn->prepare("UPDATE users SET is_verified = 1, email_verification_code = NULL WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

// Clean up pending data
$role = $_SESSION['pending_user_role'];
unset($_SESSION['pending_user_id'], $_SESSION['pending_user_name'], $_SESSION['pending_user_email'], $_SESSION['pending_user_role'], $_SESSION['pending_code'], $_SESSION['pending_code_time']);

// Redirect based on role
switch ($role) {
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
