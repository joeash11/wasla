<?php
// ============================================
// Logout API - Destroys session
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'session.php';
session_destroy();
echo json_encode(['success' => true, 'message' => 'Logged out']);
?>
