<?php
// ============================================
// Signup Handler - Processes signup form
// ============================================
session_start();
require_once __DIR__ . '/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$first_name   = trim($_POST['first_name'] ?? '');
$last_name    = trim($_POST['last_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$password     = $_POST['password'] ?? '';
$role         = $_POST['role'] ?? 'client';
$company_name = trim($_POST['company_name'] ?? '');
$city         = trim($_POST['city'] ?? '');
$skills       = trim($_POST['skills'] ?? '');
$category     = trim($_POST['category'] ?? '');
if ($category === '') {
    $category = null;
}

// Validate required fields
if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    header('Location: signup.php?error=empty');
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    header('Location: signup.php?error=exists');
    exit;
}
$stmt->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, role, company_name, city, skills, category, is_verified, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)");
$stmt->bind_param("ssssssssss", $first_name, $last_name, $email, $hashed_password, $phone, $role, $company_name, $city, $skills, $category);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    // Generate 6-digit verification code for the new account
    $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

    // Save code to database
    $update_stmt = $conn->prepare("UPDATE users SET email_verification_code = ? WHERE id = ?");
    $update_stmt->bind_param("si", $code, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Store pending login data in session for the verification screen
    $_SESSION['pending_user_id']   = $user_id;
    $_SESSION['pending_user_name'] = $first_name . ' ' . $last_name;
    $_SESSION['pending_user_email']= $email;
    $_SESSION['pending_user_role'] = $role;
    $_SESSION['pending_code']      = $code;
    $_SESSION['pending_code_time'] = time();

    // Redirect to verification UI
    header('Location: verify-email.php');
} else {
    header('Location: signup.php?error=failed');
}

$stmt->close();
exit;
?>
