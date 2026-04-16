<?php
// ============================================
// Client Guard - Include at top of client pages
// Redirects to login if not authenticated
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php?error=auth');
    exit;
}

// If user is usher, redirect to usher dashboard
if ($_SESSION['user_role'] === 'usher') {
    header('Location: usher/dashboard.php');
    exit;
}

// If user is admin, redirect to admin dashboard
if ($_SESSION['user_role'] === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

// Make user info available to the page
$user_id    = $_SESSION['user_id'];
$user_name  = $_SESSION['user_name'] ?? 'User';
$first_name = $_SESSION['first_name'] ?? 'User';
$user_email = $_SESSION['user_email'] ?? '';
$user_role  = $_SESSION['user_role'] ?? 'client';
?>
