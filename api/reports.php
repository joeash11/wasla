<?php
// ============================================
// Reports API
// Handles: submit report, list reports, 
// get reportable users for a project
// ============================================
session_start();
header('Content-Type: application/json');

// Check auth
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Please login first']);
    exit;
}

require_once __DIR__ . '/../db/connection.php';

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// ===================== GET: List reportable users or my reports =====================
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'my_reports';
    
    // Get users I can report for a specific project
    if ($action === 'reportable_users' && isset($_GET['project_id'])) {
        $project_id = intval($_GET['project_id']);
        
        if ($user_role === 'client') {
            // Client can report ushers who were accepted/completed in their project
            $sql = "SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS name, u.email, pa.status AS app_status
                    FROM project_applications pa
                    JOIN users u ON pa.usher_id = u.id
                    JOIN projects p ON pa.project_id = p.id
                    WHERE pa.project_id = ? AND p.client_id = ? 
                    AND pa.status IN ('accepted', 'completed')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $project_id, $user_id);
        } elseif ($user_role === 'usher') {
            // Usher can report the client who owns a project they worked on
            $sql = "SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS name, u.company_name, u.email
                    FROM projects p
                    JOIN users u ON p.client_id = u.id
                    JOIN project_applications pa ON pa.project_id = p.id
                    WHERE p.id = ? AND pa.usher_id = ? 
                    AND pa.status IN ('accepted', 'completed')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $project_id, $user_id);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid role']);
            exit;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
        echo json_encode(['success' => true, 'users' => $users]);
        exit;
    }
    
    // Get my projects (for the report form dropdown)
    if ($action === 'my_projects') {
        if ($user_role === 'client') {
            $sql = "SELECT p.id, p.title, p.event_date, p.status 
                    FROM projects p 
                    WHERE p.client_id = ? AND p.status IN ('active', 'completed')
                    ORDER BY p.event_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
        } elseif ($user_role === 'usher') {
            $sql = "SELECT p.id, p.title, p.event_date, p.status
                    FROM project_applications pa
                    JOIN projects p ON pa.project_id = p.id
                    WHERE pa.usher_id = ? AND pa.status IN ('accepted', 'completed')
                    ORDER BY p.event_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
        } else {
            echo json_encode(['success' => true, 'projects' => []]);
            exit;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = [];
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        $stmt->close();
        echo json_encode(['success' => true, 'projects' => $projects]);
        exit;
    }
    
    // Get my submitted reports
    if ($action === 'my_reports') {
        $sql = "SELECT r.*, 
                       CONCAT(u.first_name, ' ', u.last_name) AS reported_name,
                       p.title AS project_title
                FROM reports r
                JOIN users u ON r.reported_id = u.id
                JOIN projects p ON r.project_id = p.id
                WHERE r.reporter_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        $stmt->close();
        echo json_encode(['success' => true, 'reports' => $reports]);
        exit;
    }
    
    // Admin: get all reports
    if ($action === 'all_reports' && $user_role === 'admin') {
        $sql = "SELECT r.*, 
                       CONCAT(reporter.first_name, ' ', reporter.last_name) AS reporter_name,
                       reporter.email AS reporter_email,
                       CONCAT(reported.first_name, ' ', reported.last_name) AS reported_name,
                       reported.email AS reported_email,
                       p.title AS project_title
                FROM reports r
                JOIN users reporter ON r.reporter_id = reporter.id
                JOIN users reported ON r.reported_id = reported.id
                JOIN projects p ON r.project_id = p.id
                ORDER BY 
                    CASE r.status 
                        WHEN 'pending' THEN 1 
                        WHEN 'reviewed' THEN 2 
                        WHEN 'resolved' THEN 3 
                        WHEN 'dismissed' THEN 4 
                    END,
                    r.created_at DESC";
        $result = $conn->query($sql);
        $reports = [];
        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }
        
        // Stats
        $stats = ['total' => 0, 'pending' => 0, 'reviewed' => 0, 'resolved' => 0, 'dismissed' => 0];
        foreach ($reports as $r) {
            $stats['total']++;
            if (isset($stats[$r['status']])) $stats[$r['status']]++;
        }
        
        echo json_encode(['success' => true, 'reports' => $reports, 'stats' => $stats]);
        exit;
    }
}

// ===================== POST: Submit a report or admin action =====================
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Admin action on a report
    if (isset($data['report_id']) && isset($data['action']) && $user_role === 'admin') {
        $report_id = intval($data['report_id']);
        $action = $data['action'];
        $admin_notes = trim($data['admin_notes'] ?? '');
        
        $valid_actions = ['reviewed', 'resolved', 'dismissed'];
        if (!in_array($action, $valid_actions)) {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            exit;
        }
        
        $stmt = $conn->prepare("UPDATE reports SET status = ?, admin_notes = ? WHERE id = ?");
        $stmt->bind_param("ssi", $action, $admin_notes, $report_id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => "Report marked as $action"]);
        exit;
    }
    
    // Submit a new report
    if (!$data || !isset($data['reported_id']) || !isset($data['project_id']) || !isset($data['reason']) || !isset($data['description'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    $reported_id = intval($data['reported_id']);
    $project_id = intval($data['project_id']);
    $reason = $data['reason'];
    $description = trim($data['description']);
    $reporter_role = $user_role;
    
    // Validate reason
    $valid_reasons = ['unprofessional', 'no_show', 'harassment', 'late', 'payment_issue', 'safety', 'other'];
    if (!in_array($reason, $valid_reasons)) {
        echo json_encode(['success' => false, 'error' => 'Invalid reason']);
        exit;
    }
    
    // Can't report yourself
    if ($reported_id === $user_id) {
        echo json_encode(['success' => false, 'error' => 'You cannot report yourself']);
        exit;
    }
    
    // Check for duplicate report
    $stmt = $conn->prepare("SELECT id FROM reports WHERE reporter_id = ? AND reported_id = ? AND project_id = ?");
    $stmt->bind_param("iii", $user_id, $reported_id, $project_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'You have already submitted a report for this person on this project']);
        exit;
    }
    $stmt->close();
    
    // Insert report
    $stmt = $conn->prepare(
        "INSERT INTO reports (reporter_id, reported_id, project_id, reporter_role, reason, description) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("iiisss", $user_id, $reported_id, $project_id, $reporter_role, $reason, $description);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your report has been submitted and will be reviewed by an administrator.',
            'report_id' => $stmt->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to submit report']);
    }
    $stmt->close();
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
?>
