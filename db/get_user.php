<?php
// ============================================
// Get Current User Session
// Returns logged-in user info or error
// ============================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'connection.php';
require_once 'session.php';

if (!isLoggedIn()) {
    echo json_encode(['logged_in' => false]);
    exit;
}

$user_id = getCurrentUserId();
$sql = "SELECT id, first_name, last_name, email, phone, role, company_name, city, bio, profile_image, rating, skills, created_at 
        FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    echo json_encode(['logged_in' => false]);
    exit;
}

$user = $result->fetch_assoc();
echo json_encode([
    'logged_in' => true,
    'user' => $user
]);

$stmt->close();
$conn->close();
?>
