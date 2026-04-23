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

// Verify role matches and secure the admin login
if ($user['role'] === 'admin') {
    // Admins must strictly use the admin tab and the specific admin email
    if ($email !== 'admin@wasla.com' || $role !== 'admin') {
        header('Location: login.php?error=unauthorized');
        exit;
    }
} else {
    // For Clients and Ushers: 
    // Do not allow them to use the Admin tab.
    if ($role === 'admin') {
        header('Location: login.php?error=role');
        exit;
    }
    // We ignore if they are on the 'client' tab but their DB role is 'usher' 
    // because they will naturally be routed correctly. This prevents the "logout lock" bug.
}

// Generate 6-digit verification code - required for ALL environments

$code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

// Save code to database
$stmt = $conn->prepare("UPDATE users SET email_verification_code = ? WHERE id = ?");
$stmt->bind_param("si", $code, $user['id']);
$stmt->execute();
$stmt->close();

// Send email
$to = $user['email'];
$subject = "Wasla - Your Login Verification Code";
$message = "Your Wasla verification code is: $code\n\nThis code expires in 10 minutes.\nIf you didn't request this, please ignore this email.";
$headers = "From: noreply@wasla.com\r\nContent-Type: text/plain; charset=UTF-8";

@mail($to, $subject, $message, $headers);

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
