<?php
require_once __DIR__ . '/connection.php';

echo "<h2>Seeding data for Ahmed Mamdouh (user_id=1)...</h2>";

// First, create sample clients if they don't exist
$clients = [
    ['Sara', 'Al-Rashid', 'sara@mdlbeast.com', 'MDLBEAST', 'Riyadh'],
    ['Abdullah', 'Elsayed', 'abdullah@gulfevent.com', 'Gulf Events Co.', 'Riyadh'],
    ['Nora', 'Al-Fahad', 'nora@visionary.sa', 'Visionary Events', 'Jeddah'],
    ['Omar', 'Hassan', 'omar@techsummit.ae', 'TechSummit UAE', 'Dubai'],
];

$client_ids = [];
foreach ($clients as $c) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $c[2]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $client_ids[] = $result->fetch_assoc()['id'];
    } else {
        $hash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, role, company_name, city, is_verified, is_active) VALUES (?, ?, ?, ?, '+966 555 000 000', 'client', ?, ?, 1, 1)");
        $stmt2->bind_param("ssssss", $c[0], $c[1], $c[2], $hash, $c[3], $c[4]);
        $stmt2->execute();
        $client_ids[] = $stmt2->insert_id;
        $stmt2->close();
    }
    $stmt->close();
}

echo "<p>✅ Created/found " . count($client_ids) . " client accounts</p>";

// Create projects
$projects_data = [
    [$client_ids[0], 'MDLBEAST Soundstorm 2024', 'The biggest music festival in the Middle East featuring international artists.', '2024-12-19', '2024-12-21', 'Banban, Riyadh', 'Riyadh', 50, 600.00, 'completed', 'Festival'],
    [$client_ids[1], 'Gaming Festival 2024', 'Annual gaming event featuring esports tournaments and exhibitions.', '2024-11-15', '2024-11-16', 'Riyadh Exhibition Center', 'Riyadh', 20, 450.00, 'completed', 'Festival'],
    [$client_ids[2], 'Fashion Week Jeddah', 'International fashion week showcasing top designers.', '2024-10-10', '2024-10-15', 'The Ritz-Carlton Jeddah', 'Jeddah', 15, 550.00, 'completed', 'Fashion'],
    [$client_ids[1], 'Annual Charity Gala', 'Black-tie charity dinner and celebrity fundraiser.', '2024-09-05', '2024-09-05', 'Al Faisaliyah Hotel', 'Riyadh', 8, 380.00, 'completed', 'Corporate'],
    [$client_ids[3], 'Corporate Summit KSA', 'Business leadership conference with 2000+ attendees.', '2025-01-10', '2025-01-11', 'Hilton Riyadh', 'Riyadh', 12, 400.00, 'completed', 'Corporate'],
    [$client_ids[0], 'Riyadh Season Opening', 'Grand launch event for Riyadh Season entertainment zone.', '2025-02-01', '2025-02-03', 'Boulevard Riyadh', 'Riyadh', 40, 520.00, 'completed', 'Festival'],
    [$client_ids[3], 'Tech Innovation Expo', 'Technology and startup exhibition with demos and networking.', '2025-03-05', '2025-03-06', 'DIFC Dubai', 'Dubai', 18, 500.00, 'completed', 'Corporate'],
    [$client_ids[2], 'Luxury Auto Show', 'Premium automotive exhibition featuring supercars.', '2025-03-20', '2025-03-22', 'Jeddah Superdome', 'Jeddah', 10, 470.00, 'completed', 'Exhibition'],
    // Active/upcoming projects
    [$client_ids[0], 'MDLBEAST XP 2026', 'Music and gaming crossover festival.', '2026-05-15', '2026-05-17', 'Diriyah, Riyadh', 'Riyadh', 35, 650.00, 'active', 'Festival'],
    [$client_ids[1], 'Saudi Cup Weekend', 'Horse racing event with VIP hospitality.', '2026-05-22', '2026-05-23', 'King Abdulaziz Racecourse', 'Riyadh', 25, 580.00, 'active', 'Sports'],
    [$client_ids[3], 'Dubai Design Week', 'Art and design festival.', '2026-06-01', '2026-06-05', 'Dubai Design District', 'Dubai', 20, 520.00, 'active', 'Exhibition'],
    [$client_ids[2], 'Red Sea Film Festival', 'International film festival and premieres.', '2026-06-10', '2026-06-14', 'Jeddah Waterfront', 'Jeddah', 30, 600.00, 'active', 'Entertainment'],
    [$client_ids[1], 'Food Expo Riyadh', 'International culinary arts and food festival.', '2026-07-01', '2026-07-03', 'Riyadh Front', 'Riyadh', 15, 350.00, 'pending', 'Festival'],
    [$client_ids[0], 'Formula E Riyadh', 'Electric racing championship event.', '2026-07-20', '2026-07-21', 'Ad Diriyah Circuit', 'Riyadh', 40, 700.00, 'pending', 'Sports'],
];

$project_ids = [];
// Delete old projects to avoid duplicates
$conn->query("DELETE FROM project_applications WHERE usher_id = 1");
$conn->query("DELETE FROM reviews WHERE usher_id = 1");
$conn->query("DELETE FROM transactions WHERE payee_id = 1");

foreach ($projects_data as $p) {
    $stmt = $conn->prepare("INSERT INTO projects (client_id, title, description, event_date, end_date, location, city, ushers_needed, pay_per_usher, status, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssidss", $p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10]);
    $stmt->execute();
    $project_ids[] = $stmt->insert_id;
    $stmt->close();
}
echo "<p>✅ Created " . count($project_ids) . " projects</p>";

// Applications for Ahmed (user_id = 1)
$applications = [
    // Completed gigs (8)
    [$project_ids[0], 'completed', '2024-12-01 10:00:00', '2024-12-02 09:00:00'],
    [$project_ids[1], 'completed', '2024-10-30 14:00:00', '2024-10-31 11:00:00'],
    [$project_ids[2], 'completed', '2024-09-25 09:00:00', '2024-09-26 10:00:00'],
    [$project_ids[3], 'completed', '2024-08-20 12:00:00', '2024-08-21 08:00:00'],
    [$project_ids[4], 'completed', '2024-12-28 16:00:00', '2024-12-29 10:00:00'],
    [$project_ids[5], 'completed', '2025-01-15 08:00:00', '2025-01-16 09:00:00'],
    [$project_ids[6], 'completed', '2025-02-20 11:00:00', '2025-02-21 14:00:00'],
    [$project_ids[7], 'completed', '2025-03-10 09:00:00', '2025-03-11 10:00:00'],
    // Accepted upcoming gigs (4)
    [$project_ids[8], 'accepted', '2026-04-01 10:00:00', '2026-04-02 09:00:00'],
    [$project_ids[9], 'accepted', '2026-04-05 14:00:00', '2026-04-06 11:00:00'],
    [$project_ids[10], 'accepted', '2026-04-08 09:00:00', '2026-04-09 10:00:00'],
    [$project_ids[11], 'accepted', '2026-04-10 12:00:00', '2026-04-11 08:00:00'],
];

foreach ($applications as $a) {
    $stmt = $conn->prepare("INSERT INTO project_applications (project_id, usher_id, status, applied_at, responded_at) VALUES (?, 1, ?, ?, ?)");
    $stmt->bind_param("isss", $a[0], $a[1], $a[2], $a[3]);
    $stmt->execute();
    $stmt->close();
}
echo "<p>✅ Created " . count($applications) . " job applications (8 completed, 4 upcoming)</p>";

// Reviews for Ahmed
$reviews = [
    [$client_ids[0], $project_ids[0], 5, 'Ahmed was outstanding — professional, punctual, and amazing with VIP guests. Will definitely hire again!', '2024-12-22 18:00:00'],
    [$client_ids[1], $project_ids[1], 5, 'One of the best ushers we have worked with. Great energy and professionalism.', '2024-11-18 15:00:00'],
    [$client_ids[2], $project_ids[2], 4, 'Very reliable and handled the fashion week crowd with perfect etiquette.', '2024-10-16 22:00:00'],
    [$client_ids[1], $project_ids[3], 5, 'Excellent work at the charity gala. Very well-presented and charming.', '2024-09-06 17:00:00'],
    [$client_ids[3], $project_ids[4], 4, 'Strong communication skills and great with corporate attendees.', '2025-01-12 14:00:00'],
    [$client_ids[0], $project_ids[5], 5, 'Ahmed handled the massive crowd perfectly. True professional!', '2025-02-04 20:00:00'],
    [$client_ids[3], $project_ids[6], 5, 'Bilingual skills were a huge asset at the tech expo. Highly recommended.', '2025-03-07 16:00:00'],
    [$client_ids[2], $project_ids[7], 4, 'Great presence at the auto show. Guests loved his energy.', '2025-03-23 19:00:00'],
];

foreach ($reviews as $r) {
    $stmt = $conn->prepare("INSERT INTO reviews (reviewer_id, usher_id, project_id, rating, comment, created_at) VALUES (?, 1, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $r[0], $r[1], $r[2], $r[3], $r[4]);
    $stmt->execute();
    $stmt->close();
}
echo "<p>✅ Created " . count($reviews) . " reviews (avg 4.6 stars)</p>";

// Transactions (earnings)
$transactions = [
    [$client_ids[0], $project_ids[0], 600.00, 'Payment for MDLBEAST Soundstorm 2024', '2024-12-23 12:00:00'],
    [$client_ids[0], $project_ids[0], 200.00, 'Bonus - MDLBEAST Soundstorm', '2024-12-24 10:00:00'],
    [$client_ids[1], $project_ids[1], 450.00, 'Payment for Gaming Festival 2024', '2024-11-18 12:00:00'],
    [$client_ids[2], $project_ids[2], 550.00, 'Payment for Fashion Week Jeddah', '2024-10-17 12:00:00'],
    [$client_ids[2], $project_ids[2], 150.00, 'Tips - Fashion Week Jeddah', '2024-10-18 09:00:00'],
    [$client_ids[1], $project_ids[3], 380.00, 'Payment for Annual Charity Gala', '2024-09-07 12:00:00'],
    [$client_ids[3], $project_ids[4], 400.00, 'Payment for Corporate Summit KSA', '2025-01-13 12:00:00'],
    [$client_ids[0], $project_ids[5], 520.00, 'Payment for Riyadh Season Opening', '2025-02-05 12:00:00'],
    [$client_ids[0], $project_ids[5], 300.00, 'Overtime bonus - Riyadh Season', '2025-02-06 10:00:00'],
    [$client_ids[3], $project_ids[6], 500.00, 'Payment for Tech Innovation Expo', '2025-03-08 12:00:00'],
    [$client_ids[2], $project_ids[7], 470.00, 'Payment for Luxury Auto Show', '2025-03-24 12:00:00'],
    // Recent pending payouts
    [$client_ids[0], $project_ids[8], 650.00, 'Advance for MDLBEAST XP 2026', '2026-04-10 14:00:00'],
    [$client_ids[1], $project_ids[9], 580.00, 'Advance for Saudi Cup Weekend', '2026-04-12 10:00:00'],
];

foreach ($transactions as $t) {
    $status = (strtotime($t[4]) < strtotime('2026-01-01')) ? 'completed' : 'pending';
    $stmt = $conn->prepare("INSERT INTO transactions (payer_id, payee_id, project_id, amount, type, status, description, created_at) VALUES (?, 1, ?, ?, 'payout', ?, ?, ?)");
    $stmt->bind_param("iidsss", $t[0], $t[1], $t[2], $status, $t[3], $t[4]);
    $stmt->execute();
    $stmt->close();
}
echo "<p>✅ Created " . count($transactions) . " transactions (SAR 5,750 total earnings)</p>";

// Update Ahmed's rating and skills
$conn->query("UPDATE users SET rating = 4.63, skills = 'Customer Service, Bilingual (AR/EN), VIP Handling, Crowd Management, Event Coordination', category = 'Entertainment', city = 'Riyadh', bio = 'Professional event usher with 2+ years of experience in major festivals, corporate events, and fashion shows across Saudi Arabia and UAE.', is_verified = 1 WHERE id = 1");
echo "<p>✅ Updated profile: rating 4.63, skills, bio</p>";

echo "<br><h2>🎉 Done! Your account is now loaded with data.</h2>";
echo "<p><a href='/wasla/login.html'>Go to Login →</a></p>";
$conn->close();
?>
