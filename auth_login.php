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

if ($user['role'] === 'admin') {
    // Bypass verification for admin
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];
    $_SESSION['logged_in']  = true;

    // Mark user as verified in DB
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, email_verification_code = NULL WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->close();

    header('Location: admin/dashboard.php');
    exit;
}

// Generate 6-digit verification code - required for ALL environments

$code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

// Save code to database
$stmt = $conn->prepare("UPDATE users SET email_verification_code = ? WHERE id = ?");
$stmt->bind_param("si", $code, $user['id']);
$stmt->execute();
$stmt->close();

// Send email via SMTP (PHPMailer)
require_once __DIR__ . '/includes/mailer.php';
$toName = trim($user['first_name'] . ' ' . $user['last_name']);
sendVerificationEmail($user['email'], $toName, $code);

// Store pending login data in session
$_SESSION['pending_user_id']   = $user['id'];
$_SESSION['pending_user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['pending_user_email']= $user['email'];
$_SESSION['pending_user_role'] = $user['role'];
$_SESSION['pending_code']      = $code;
$_SESSION['pending_code_time'] = time();

// Redirect to verification page
header('Location: verify-email.php');
exit;
?>
