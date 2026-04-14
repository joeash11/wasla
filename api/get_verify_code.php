<?php
// ============================================
// Dev helper: returns the pending verification code
// So the user can test on localhost where mail() doesn't work
// REMOVE THIS FILE IN PRODUCTION
// ============================================
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['pending_code'])) {
    echo json_encode(['code' => $_SESSION['pending_code']]);
} else {
    echo json_encode(['code' => null]);
}
?>
