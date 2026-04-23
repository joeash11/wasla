<?php
require_once __DIR__ . '/db/connection.php';

// Only allow running from localhost
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'])) {
    die('Access denied');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? 'usher';

    if ($email && $pass && $first) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_verified, is_active) VALUES (?, ?, ?, ?, ?, 1, 1) ON DUPLICATE KEY UPDATE password = VALUES(password), is_active = 1, is_verified = 1");
        $stmt->bind_param("sssss", $first, $last, $email, $hash, $role);
        if ($stmt->execute()) {
            $message = "✅ Account created/updated! <a href='login.php'>Go to Login →</a>";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Show all current users
$users = $conn->query("SELECT id, first_name, last_name, email, role, is_active FROM users ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Account – Wasla Dev Tool</title>
    <style>
        body { font-family: sans-serif; max-width: 700px; margin: 40px auto; background: #0f0f1a; color: #eee; padding: 20px; }
        h2 { color: #00bcd4; }
        input, select { width: 100%; padding: 10px; margin: 6px 0 14px; border-radius: 6px; border: 1px solid #333; background: #1a1a2e; color: #eee; }
        button { background: #00bcd4; color: #000; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 1rem; }
        label { font-size: 0.9rem; color: #aaa; }
        .msg { background: #1a3a1a; padding: 12px; border-radius: 6px; margin-bottom: 20px; color: #4caf50; }
        .msg a { color: #00bcd4; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #222; font-size: 0.85rem; }
        th { background: #1a1a2e; color: #00bcd4; }
        .role-usher { color: #4fc3f7; }
        .role-client { color: #81c784; }
        .role-admin { color: #ef9a9a; }
    </style>
</head>
<body>
    <h2>🛠 Wasla Dev Tool – Create / Reset Account</h2>
    <p style="color:#888">This page only works on localhost. <strong>Delete this file before going live.</strong></p>

    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>First Name</label>
        <input name="first_name" required value="Ahmed">

        <label>Last Name</label>
        <input name="last_name" value="Mamdouh">

        <label>Email</label>
        <input type="email" name="email" required value="ahmed@gmail.com">

        <label>Password</label>
        <input type="text" name="password" required value="123456">

        <label>Role</label>
        <select name="role">
            <option value="usher" selected>Usher</option>
            <option value="client">Client</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Create / Update Account</button>
    </form>

    <table>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th></tr>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td class="role-<?= $u['role'] ?>"><?= $u['role'] ?></td>
            <td><?= $u['is_active'] ? '✅' : '❌' ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
