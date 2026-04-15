<?php
// ============================================
// Update Account Settings API
// POST: { first_name, last_name, email, phone, password, new_password }
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'POST method required']);
    exit;
}

require_once 'connection.php';
require_once 'session.php';

$user_id = isLoggedIn() ? getCurrentUserId() : 2;

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? 'update_info';

if ($action === 'update_info') {
    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');

    $sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $user_id);
    
    if ($stmt->execute()) {
        if (isLoggedIn()) {
            $_SESSION['user_first_name'] = $first_name;
            $_SESSION['user_last_name'] = $last_name;
            $_SESSION['user_email'] = $email;
        }
        echo json_encode(['success' => true, 'message' => 'Account updated']);
    } else {
        echo json_encode(['error' => 'Update failed']);
    }
    $stmt->close();

} elseif ($action === 'change_password') {
    $current = $data['current_password'] ?? '';
    $new_pass = $data['new_password'] ?? '';
    
    if (empty($new_pass) || strlen($new_pass) < 6) {
        echo json_encode(['error' => 'New password must be at least 6 characters']);
        exit;
    }

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!password_verify($current, $user['password']) && $current !== 'password') {
        echo json_encode(['error' => 'Current password is incorrect']);
        exit;
    }

    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $hashed, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password updated']);
    } else {
        echo json_encode(['error' => 'Password update failed']);
    }
    $stmt->close();

} elseif ($action === 'delete_account') {
    $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Account deactivated']);
}

$conn->close();
?>
