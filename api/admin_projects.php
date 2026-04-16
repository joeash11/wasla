<?php
// ============================================
// Admin Projects API
// Handles: list, approve, decline, flag, remove
// ============================================
session_start();
header('Content-Type: application/json');

// Check admin auth
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../db/connection.php';

$method = $_SERVER['REQUEST_METHOD'];

// ===================== GET: List all projects =====================
if ($method === 'GET') {
    $sql = "SELECT p.*, 
                   CONCAT(u.first_name, ' ', u.last_name) AS client_name,
                   u.company_name,
                   (SELECT COUNT(*) FROM project_applications pa WHERE pa.project_id = p.id AND pa.status IN ('accepted','completed')) AS accepted_ushers
            FROM projects p
            JOIN users u ON p.client_id = u.id
            ORDER BY 
                CASE p.status 
                    WHEN 'pending' THEN 1 
                    WHEN 'active' THEN 2 
                    WHEN 'completed' THEN 3 
                    WHEN 'cancelled' THEN 4 
                END,
                p.created_at DESC";
    
    $result = $conn->query($sql);
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    
    // Stats
    $stats = [
        'total' => 0,
        'pending' => 0,
        'active' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    foreach ($projects as $p) {
        $stats['total']++;
        if (isset($stats[$p['status']])) {
            $stats[$p['status']]++;
        }
    }
    
    echo json_encode(['success' => true, 'projects' => $projects, 'stats' => $stats]);
    exit;
}

// ===================== POST: Update project status =====================
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['project_id']) || !isset($data['action'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing project_id or action']);
        exit;
    }
    
    $project_id = intval($data['project_id']);
    $action = $data['action'];
    
    // Validate project exists
    $stmt = $conn->prepare("SELECT id, status, title FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Project not found']);
        exit;
    }
    
    $project = $result->fetch_assoc();
    $stmt->close();
    
    switch ($action) {
        case 'approve':
            // Only pending projects can be approved
            if ($project['status'] !== 'pending') {
                echo json_encode(['success' => false, 'error' => 'Only pending projects can be approved']);
                exit;
            }
            $new_status = 'active';
            break;
            
        case 'decline':
            // Only pending projects can be declined
            if ($project['status'] !== 'pending') {
                echo json_encode(['success' => false, 'error' => 'Only pending projects can be declined']);
                exit;
            }
            $new_status = 'cancelled';
            break;
            
        case 'flag':
            // Active projects can be flagged (cancelled)
            $new_status = 'cancelled';
            break;
            
        case 'reactivate':
            // Cancelled projects can be reactivated
            if ($project['status'] !== 'cancelled') {
                echo json_encode(['success' => false, 'error' => 'Only cancelled projects can be reactivated']);
                exit;
            }
            $new_status = 'active';
            break;
            
        case 'remove':
            // Permanently delete the project
            $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Project removed permanently']);
            exit;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
            exit;
    }
    
    // Update project status
    $stmt = $conn->prepare("UPDATE projects SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $project_id);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode([
        'success' => true, 
        'message' => "Project \"{$project['title']}\" has been " . ($action === 'approve' ? 'approved' : ($action === 'decline' ? 'declined' : $action . 'ed')),
        'new_status' => $new_status
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
?>
