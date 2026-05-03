<?php
require_once __DIR__ . '/db/connection.php';

// Get a client ID (first client found)
$result = $conn->query("SELECT id FROM users WHERE role = 'client' LIMIT 1");
if ($result && $result->num_rows > 0) {
    $client_id = $result->fetch_assoc()['id'];

    $projects_data = [
        [$client_id, 'Cairo Tech Summit', 'Annual gathering of tech enthusiasts and startups in Egypt.', '2026-08-15', '2026-08-17', 'Al Manara Center', 'Cairo', 50, 450.00, 'active', 'Conference'],
        [$client_id, 'Alexandria Summer Festival', 'Music and arts festival by the sea.', '2026-07-10', '2026-07-12', 'Bibliotheca Alexandrina', 'Alexandria', 30, 350.00, 'active', 'Festival'],
        [$client_id, 'Gouna Film Festival', 'Prestigious film festival requiring VIP handling.', '2026-09-20', '2026-09-25', 'El Gouna Resort', 'El Gouna', 40, 700.00, 'pending', 'Entertainment'],
        [$client_id, 'Sharm Sports Tournament', 'International beach volleyball tournament.', '2026-11-05', '2026-11-08', 'Sharm Elsheikh Beach', 'Sharm Elsheikh', 25, 400.00, 'active', 'Sports'],
        [$client_id, 'North Coast Concert Series', 'Weekend concerts featuring top artists.', '2026-08-01', '2026-08-02', 'Marassi', 'North Coast', 60, 500.00, 'active', 'Concert']
    ];

    foreach ($projects_data as $p) {
        $stmt = $conn->prepare("INSERT INTO projects (client_id, title, description, event_date, end_date, location, city, ushers_needed, pay_per_usher, status, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssidss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10]);
        $stmt->execute();
        $stmt->close();
    }
    echo "Added " . count($projects_data) . " new projects!\n";
} else {
    echo "No clients found to associate projects with.\n";
}
$conn->close();
?>
