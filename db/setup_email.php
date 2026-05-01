<?php
// ============================================
// SMTP Settings Migration
// Run this ONCE to add email settings to DB
// Visit: http://localhost/grad%20proj/db/setup_email.php
// ============================================
require_once __DIR__ . '/connection.php';

// Create smtp_settings table
$conn->query("
    CREATE TABLE IF NOT EXISTS smtp_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        smtp_host VARCHAR(100) NOT NULL DEFAULT 'smtp.gmail.com',
        smtp_port INT NOT NULL DEFAULT 587,
        smtp_username VARCHAR(150) NOT NULL DEFAULT '',
        smtp_password VARCHAR(255) NOT NULL DEFAULT '',
        smtp_from_email VARCHAR(150) NOT NULL DEFAULT 'noreply@wasla.com',
        smtp_from_name VARCHAR(100) NOT NULL DEFAULT 'Wasla',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Insert a default row if empty
$result = $conn->query("SELECT COUNT(*) as cnt FROM smtp_settings");
$row = $result->fetch_assoc();
if ($row['cnt'] == 0) {
    $conn->query("
        INSERT INTO smtp_settings (smtp_host, smtp_port, smtp_username, smtp_password, smtp_from_email, smtp_from_name)
        VALUES ('smtp.gmail.com', 587, '', '', 'noreply@wasla.com', 'Wasla')
    ");
    echo "✅ smtp_settings table created and default row inserted.<br>";
} else {
    echo "✅ smtp_settings table already exists.<br>";
}

echo "<br><strong>Setup complete.</strong> Now go to <a href='../admin/email_settings.php'>Admin → Email Settings</a> to enter your SMTP credentials.";
?>
