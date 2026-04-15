<?php
// ============================================
// Signup API
// POST: { first_name, last_name, email, password, phone, role, company_name, city, skills }
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

$data = json_decode(file_get_contents('php://input'), true);

$first_name = trim($data['first_name'] ?? '');
$last_name = trim($data['last_name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$phone = trim($data['phone'] ?? '');
$role = $data['role'] ?? 'client';
$company_name = trim($data['company_name'] ?? '');
$city = trim($data['city'] ?? '');
$skills = trim($data['skills'] ?? '');

if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    echo json_encode(['error' => 'First name, last name, email, and password are required']);
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['error' => 'Email already registered']);
    $check->close();
    exit;
}
$check->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (first_name, last_name, email, password, phone, role, company_name, city, skills, is_verified, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssss", $first_name, $last_name, $email, $hashed_password, $phone, $role, $company_name, $city, $skills);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    
    // Set session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_first_name'] = $first_name;
    $_SESSION['user_last_name'] = $last_name;
    $_SESSION['user_role'] = $role;
    
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'role' => $role
        ]
    ]);
} else {
    echo json_encode(['error' => 'Failed to create account: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
