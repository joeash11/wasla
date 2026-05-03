<?php
require_once __DIR__ . '/connection.php';

echo "<h2>Seeding 20 new diverse Ushers and applications...</h2>";

$usher_names = [
    ['Mohammed', 'Ali', 'm.ali@example.com', 'Riyadh'],
    ['Fatima', 'Zahra', 'fatima.z@example.com', 'Jeddah'],
    ['Yousef', 'Ahmed', 'yousef.a@example.com', 'Cairo'],
    ['Nour', 'El Houda', 'nour.e@example.com', 'Dubai'],
    ['Karim', 'Hassan', 'karim.h@example.com', 'Riyadh'],
    ['Lina', 'Mahmoud', 'lina.m@example.com', 'Alexandria'],
    ['Tariq', 'Saeed', 'tariq.s@example.com', 'Sharm Elsheikh'],
    ['Hala', 'Mansour', 'hala.m@example.com', 'North Coast'],
    ['Omar', 'Farooq', 'omar.f@example.com', 'El Gouna'],
    ['Salma', 'Ibrahim', 'salma.i@example.com', 'Cairo'],
    ['Khalid', 'Abdulrahman', 'khalid.a@example.com', 'Jeddah'],
    ['Amira', 'Tawfiq', 'amira.t@example.com', 'Dubai'],
    ['Ziad', 'Nasser', 'ziad.n@example.com', 'Riyadh'],
    ['Yasmin', 'Fouad', 'yasmin.f@example.com', 'Alexandria'],
    ['Hassan', 'Mostafa', 'hassan.m@example.com', 'Cairo'],
    ['Maha', 'Gaber', 'maha.g@example.com', 'Sharm Elsheikh'],
    ['Rami', 'Kamal', 'rami.k@example.com', 'Dubai'],
    ['Dina', 'Samir', 'dina.s@example.com', 'Jeddah'],
    ['Walid', 'Osama', 'walid.o@example.com', 'Riyadh'],
    ['Nada', 'Tarek', 'nada.t@example.com', 'El Gouna']
];

$skills_pool = [
    'Festival, VIP handling, Crowd Control',
    'Corporate, Registration, Networking',
    'Exhibition, Booth Management, Sales',
    'Fashion, Runways, Styling',
    'Sports, Ticket scanning, Energetic',
    'Entertainment, Hosting, Multilingual',
    'Festival, Sports, Registration',
    'Corporate, Exhibition, VIP handling'
];

$hash = password_hash('password123', PASSWORD_DEFAULT);
$usher_ids = [];

foreach ($usher_names as $index => $u) {
    $rating = rand(30, 50) / 10; // 3.0 to 5.0
    if ($rating == 5) $rating = 5.0;
    
    $skills = $skills_pool[array_rand($skills_pool)];
    
    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $u[2]);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $id = $res->fetch_assoc()['id'];
        $usher_ids[] = $id;
        // Update skills and rating
        $conn->query("UPDATE users SET skills = '$skills', rating = $rating WHERE id = $id");
    } else {
        $stmt2 = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, role, city, is_verified, is_active, rating, skills) VALUES (?, ?, ?, ?, '+966 555 123 456', 'usher', ?, 1, 1, ?, ?)");
        $stmt2->bind_param("ssssssd", $u[0], $u[1], $u[2], $hash, $u[3], $rating, $skills);
        $stmt2->execute();
        $usher_ids[] = $stmt2->insert_id;
        $stmt2->close();
    }
    $stmt->close();
}

echo "<p>✅ Created/Updated " . count($usher_ids) . " ushers.</p>";

// Now apply them to active and pending projects
$res = $conn->query("SELECT id, status FROM projects WHERE status IN ('active', 'pending')");
$projects = [];
while ($row = $res->fetch_assoc()) {
    $projects[] = $row['id'];
}

$applications_created = 0;
foreach ($projects as $pid) {
    // Pick 5 to 10 random ushers to apply for this project
    $num_applicants = rand(5, 12);
    $shuffled_ushers = $usher_ids;
    shuffle($shuffled_ushers);
    $selected_ushers = array_slice($shuffled_ushers, 0, $num_applicants);
    
    foreach ($selected_ushers as $uid) {
        // Check if already applied
        $check = $conn->query("SELECT id FROM project_applications WHERE project_id = $pid AND usher_id = $uid");
        if ($check->num_rows == 0) {
            $status = 'pending';
            // randomly accept 1-2
            if (rand(1, 10) > 8) $status = 'accepted';
            
            $conn->query("INSERT INTO project_applications (project_id, usher_id, status, applied_at) VALUES ($pid, $uid, '$status', NOW())");
            $applications_created++;
        }
    }
}

echo "<p>✅ Created $applications_created new job applications for active/pending projects.</p>";

// Give them some completed projects too
$res = $conn->query("SELECT id FROM projects WHERE status = 'completed'");
$comp_projects = [];
while ($row = $res->fetch_assoc()) {
    $comp_projects[] = $row['id'];
}

$comp_created = 0;
foreach ($comp_projects as $cpid) {
    $num = rand(3, 8);
    $shuffled = $usher_ids;
    shuffle($shuffled);
    $selected = array_slice($shuffled, 0, $num);
    
    foreach ($selected as $uid) {
        $check = $conn->query("SELECT id FROM project_applications WHERE project_id = $cpid AND usher_id = $uid");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO project_applications (project_id, usher_id, status, applied_at) VALUES ($cpid, $uid, 'completed', NOW())");
            $comp_created++;
        }
    }
}

echo "<p>✅ Created $comp_created completed job applications for history.</p>";

echo "<h3>Seed complete!</h3>";
$conn->close();
?>
