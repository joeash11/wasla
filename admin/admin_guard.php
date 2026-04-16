<?php
// ============================================
// Admin Guard - Include at top of admin pages
// Redirects to login if not admin
// ============================================
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.html?error=auth');
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.html?error=unauthorized');
    exit;
}

// Make user info available to the page
$admin_name  = $_SESSION['user_name'] ?? 'Admin';
$admin_email = $_SESSION['user_email'] ?? '';
?>
