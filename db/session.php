<?php
// ============================================
// Session Helper - Start session & check auth
// Include this at the top of protected PHP APIs
// ============================================
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'first_name' => $_SESSION['user_first_name'] ?? null,
        'last_name' => $_SESSION['user_last_name'] ?? null,
        'role' => $_SESSION['user_role'] ?? null,
    ];
}

function requireAuth() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
}
?>
