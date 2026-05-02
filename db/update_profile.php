<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Read input (support JSON for backward compatibility or POST for FormData)
$input = $_POST;
if (empty($input)) {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
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

// Handle CV Upload
$cv_url = null;
if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir(__DIR__ . '/../uploads/cvs')) {
        mkdir(__DIR__ . '/../uploads/cvs', 0777, true);
    }
    
    $fileInfo = pathinfo($_FILES['cv']['name']);
    $ext = strtolower($fileInfo['extension']);
    if (in_array($ext, ['pdf', 'doc', 'docx'])) {
        $filename = 'cv_user_' . $user_id . '_' . time() . '.' . $ext;
        $dest = __DIR__ . '/../uploads/cvs/' . $filename;
        if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest)) {
            $cv_url = '/uploads/cvs/' . $filename;
        }
    }
}

// Handle Profile Image Upload
$profile_image_url = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir(__DIR__ . '/../uploads/avatars')) {
        mkdir(__DIR__ . '/../uploads/avatars', 0777, true);
    }

    $fileInfo = pathinfo($_FILES['profile_image']['name']);
    $ext = strtolower($fileInfo['extension']);
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] <= $maxSize) {
        $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
        $dest = __DIR__ . '/../uploads/avatars/' . $filename;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $dest)) {
            $profile_image_url = 'uploads/avatars/' . $filename;
        }
    }
}

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
if ($cv_url !== null) { $fields[] = 'cv_path = ?'; $types .= 's'; $values[] = $cv_url; }
if ($profile_image_url !== null) { $fields[] = 'profile_image = ?'; $types .= 's'; $values[] = $profile_image_url; }

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
    echo json_encode(['success' => true, 'cv_url' => $cv_url, 'profile_image' => $profile_image_url]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
