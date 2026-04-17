<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

require_once __DIR__ . '/connection.php';

$user_id = $_SESSION['user_id'];
$first_name = $input['first_name'] ?? null;
$last_name = $input['last_name'] ?? null;
$email = $input['email'] ?? null;
$phone = $input['phone'] ?? null;
$bio = $input['bio'] ?? null;
$city = $input['city'] ?? null;
$skills_array = $input['skills'] ?? null;
$skills = $skills_array ? implode(', ', $skills_array) : null;

// Build dynamic update query
$fields = [];
$types = '';
$values = [];

if ($first_name !== null) { $fields[] = 'first_name = ?'; $types .= 's'; $values[] = $first_name; }
if ($last_name !== null) { $fields[] = 'last_name = ?'; $types .= 's'; $values[] = $last_name; }
if ($email !== null) { $fields[] = 'email = ?'; $types .= 's'; $values[] = $email; }
if ($phone !== null) { $fields[] = 'phone = ?'; $types .= 's'; $values[] = $phone; }
if ($city !== null) { $fields[] = 'city = ?'; $types .= 's'; $values[] = $city; }
if ($bio !== null) { $fields[] = 'bio = ?'; $types .= 's'; $values[] = $bio; }
if ($skills !== null) { $fields[] = 'skills = ?'; $types .= 's'; $values[] = $skills; }

if (empty($fields)) {
    echo json_encode(['success' => false, 'error' => 'No fields to update']);
    exit;
}

$types .= 'i';
$values[] = $user_id;

$sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    // Update session variables
    if ($first_name !== null) $_SESSION['first_name'] = $first_name;
    if ($last_name !== null) $_SESSION['last_name'] = $last_name;
    if ($first_name !== null || $last_name !== null) {
        $_SESSION['user_name'] = ($first_name ?? $_SESSION['first_name'] ?? '') . ' ' . ($last_name ?? $_SESSION['last_name'] ?? '');
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
