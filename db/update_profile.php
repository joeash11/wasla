<?php
// ============================================
// Update User Profile
// POST: { first_name, last_name, email, phone, bio, city }
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
requireAuth();

$data = json_decode(file_get_contents('php://input'), true);
$user_id = getCurrentUserId();

$first_name = trim($data['first_name'] ?? '');
$last_name = trim($data['last_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$bio = trim($data['bio'] ?? '');
$city = trim($data['city'] ?? '');

if (empty($first_name) || empty($last_name)) {
    echo json_encode(['error' => 'First name and last name are required']);
    exit;
}

$sql = "UPDATE users SET first_name=?, last_name=?, email=?, phone=?, bio=?, city=?, updated_at=NOW() WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $bio, $city, $user_id);

if ($stmt->execute()) {
    // Update session
    $_SESSION['user_first_name'] = $first_name;
    $_SESSION['user_last_name'] = $last_name;
    $_SESSION['user_email'] = $email;
    
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['error' => 'Failed to update profile: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
