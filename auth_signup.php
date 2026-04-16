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
    // Auto-login after signup
    $user_id = $stmt->insert_id;
    $_SESSION['user_id']    = $user_id;
    $_SESSION['user_name']  = $first_name . ' ' . $last_name;
    $_SESSION['first_name'] = $first_name;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role']  = $role;
    $_SESSION['logged_in']  = true;

    // Redirect based on role
    if ($role === 'usher') {
        header('Location: usher/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
} else {
    header('Location: signup.php?error=failed');
}

$stmt->close();
exit;
?>
