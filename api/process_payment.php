<?php
// ============================================
// Mock Payment Processing — Simulates deposit to wallet
// ============================================
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$amount = (int)($input['amount'] ?? 0);

if ($amount < 100 || $amount > 50000) {
    echo json_encode(['success' => false, 'error' => 'Amount must be between EGP 100 and EGP 50,000.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Simulate processing delay
usleep(800000); // 0.8 seconds

// Record the transaction
$description = 'Wallet deposit via credit card';
$type = 'deposit';
$status = 'completed';

$stmt = $conn->prepare("INSERT INTO transactions (payer_id, payee_id, amount, type, status, description) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iidsss", $user_id, $user_id, $amount, $type, $status, $description);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully!',
        'transaction_id' => $stmt->insert_id,
        'amount' => $amount
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to record transaction.']);
}

$stmt->close();
$conn->close();
