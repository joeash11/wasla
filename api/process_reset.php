<?php
// ============================================
// Process Password Reset — Validates token & updates password
// ============================================
header('Content-Type: application/json');
require_once __DIR__ . '/../db/connection.php';

$input = json_decode(file_get_contents('php://input'), true);
$token    = $input['token'] ?? '';
$password = $input['password'] ?? '';

if (empty($token) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Missing token or password.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters.']);
    exit;
}

// Validate token
$stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid or expired reset link.']);
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

if (strtotime($user['reset_expires']) < time()) {
    echo json_encode(['success' => false, 'error' => 'This reset link has expired. Please request a new one.']);
    exit;
}

// Update password and clear token
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
$stmt->bind_param("si", $hashed, $user['id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password updated successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update password.']);
}

$stmt->close();
$conn->close();
