<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../db/connection.php';

$client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$client_id || $_SESSION['user_role'] !== 'client') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$app_id = isset($data['application_id']) ? intval($data['application_id']) : 0;
$datetime = isset($data['datetime']) ? $data['datetime'] : '';

if (!$app_id || empty($datetime)) {
    echo json_encode(['success' => false, 'error' => 'Missing application ID or datetime']);
    exit;
}

// Get the usher_id and project info
$stmt = $conn->prepare("
    SELECT pa.usher_id, p.title, p.client_id 
    FROM project_applications pa 
    JOIN projects p ON pa.project_id = p.id 
    WHERE pa.id = ?
");
$stmt->bind_param("i", $app_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Application not found']);
    exit;
}

$appData = $res->fetch_assoc();
$stmt->close();

if ($appData['client_id'] != $client_id) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized for this project']);
    exit;
}

$usher_id = $appData['usher_id'];
$project_title = $appData['title'];

// Format the date nicely
$formattedDate = date('F j, Y, g:i a', strtotime($datetime));

// Generate a random meet link
$meetLink = 'https://meet.google.com/wasla-' . substr(md5(uniqid()), 0, 8);

// Construct the message
$message = "Hello! I would like to invite you to a brief interview for the project **$project_title** on **$formattedDate**.\n\nHere is your meeting link: $meetLink\n\nPlease let me know if this time works for you.";

// Insert into messages table
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $client_id, $usher_id, $message);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
$stmt->close();
$conn->close();
?>
