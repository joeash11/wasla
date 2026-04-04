<?php
// ============================================
// Session Check Helper
// Returns current session user info as JSON
// ============================================
header('Content-Type: application/json');
session_start();

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'role' => $_SESSION['user_role'],
        'name' => $_SESSION['user_name'],
        'first_name' => $_SESSION['first_name']
    ]);
} else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
