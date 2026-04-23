<?php
// Quick password diagnostic tool
require_once __DIR__ . '/db/connection.php';

// Update all non-admin users to have password "password123" for easy testing
$hash = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("UPDATE users SET is_active = 1, is_verified = 1 WHERE role != 'admin'");
$conn->query("UPDATE users SET password = '$hash' WHERE role != 'admin'");

echo "<h2 style='font-family:sans-serif'>✅ All non-admin accounts reset to password: <code>password123</code></h2>";
echo "<table border='1' cellpadding='8' style='font-family:sans-serif;border-collapse:collapse'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th></tr>";
$res = $conn->query("SELECT id, first_name, last_name, email, role, is_active FROM users ORDER BY id");
while ($u = $res->fetch_assoc()) {
    $pass = $u['role'] === 'admin' ? 'admin123' : 'password123';
    echo "<tr><td>{$u['id']}</td><td>{$u['first_name']} {$u['last_name']}</td><td>{$u['email']}</td><td>{$u['role']}</td><td>" . ($u['is_active'] ? '✅' : '❌') . "</td></tr>";
}
echo "</table>";
echo "<br><p style='font-family:sans-serif'>Now <a href='login.php'>Go to Login →</a></p>";
?>
