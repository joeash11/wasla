<?php require_once __DIR__ . '/admin_guard.php'; ?>
<?php
require_once __DIR__ . '/../db/connection.php';

$success = '';
$error   = '';

// ── Handle form submit ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host       = trim($_POST['smtp_host'] ?? 'smtp.gmail.com');
    $port       = (int)($_POST['smtp_port'] ?? 587);
    $username   = trim($_POST['smtp_username'] ?? '');
    $password   = trim($_POST['smtp_password'] ?? '');
    $from_email = trim($_POST['smtp_from_email'] ?? 'noreply@wasla.com');
    $from_name  = trim($_POST['smtp_from_name'] ?? 'Wasla');

    if (empty($username) || empty($password)) {
        $error = 'SMTP Username and Password are required.';
    } else {
        // Upsert (update if exists, insert if not)
        $check = $conn->query("SELECT id FROM smtp_settings LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $row = $check->fetch_assoc();
            $stmt = $conn->prepare("UPDATE smtp_settings SET smtp_host=?, smtp_port=?, smtp_username=?, smtp_password=?, smtp_from_email=?, smtp_from_name=? WHERE id=?");
            $stmt->bind_param("sissssi", $host, $port, $username, $password, $from_email, $from_name, $row['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO smtp_settings (smtp_host, smtp_port, smtp_username, smtp_password, smtp_from_email, smtp_from_name) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sissss", $host, $port, $username, $password, $from_email, $from_name);
        }
        if ($stmt->execute()) {
            $success = '✅ Email settings saved successfully!';

            // Send a test email if requested
            if (!empty($_POST['send_test'])) {
                require_once __DIR__ . '/../includes/mailer.php';
                $sent = sendVerificationEmail($username, 'Admin', '123456');
                if ($sent) {
                    $success .= ' Test email sent to ' . htmlspecialchars($username) . '.';
                } else {
                    $error = '⚠️ Settings saved but test email failed. Check your credentials and make sure your Gmail App Password is correct.';
                }
            }
        } else {
            $error = 'Database error: ' . $conn->error;
        }
    }
}

// ── Load current settings ───────────────────────────────────────────────────
$settings = null;
$result = $conn->query("SELECT * FROM smtp_settings LIMIT 1");
if ($result && $result->num_rows > 0) {
    $settings = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla Admin - Email Settings</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <style>
        .email-card { background: var(--glass-bg, #1a1a2e); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 32px; max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; color: #fff; font-size: 0.9rem; font-family: inherit; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: var(--accent, #00c9a7); background: rgba(0,201,167,0.06); }
        .form-group .hint { font-size: 0.78rem; color: rgba(255,255,255,0.35); margin-top: 5px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-save { padding: 12px 28px; background: linear-gradient(135deg, #00c9a7, #00e676); border: none; border-radius: 10px; color: #fff; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,201,167,0.3); }
        .btn-test { padding: 12px 28px; background: rgba(79,195,247,0.15); border: 1px solid rgba(79,195,247,0.3); border-radius: 10px; color: #4fc3f7; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; }
        .btn-test:hover { background: rgba(79,195,247,0.25); }
        .alert-success { background: rgba(0,200,83,0.12); border: 1px solid rgba(0,200,83,0.3); color: #00c853; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 0.9rem; }
        .alert-error { background: rgba(255,23,68,0.1); border: 1px solid rgba(255,23,68,0.3); color: #ff5252; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 0.9rem; }
        .status-indicator { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 600; }
        .status-ok { background: rgba(0,200,83,0.1); border: 1px solid rgba(0,200,83,0.3); color: #00c853; }
        .status-warn { background: rgba(255,152,0,0.1); border: 1px solid rgba(255,152,0,0.3); color: #ff9800; }
        .info-box { background: rgba(79,195,247,0.08); border: 1px solid rgba(79,195,247,0.2); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; font-size: 0.85rem; color: rgba(255,255,255,0.65); line-height: 1.7; }
        .info-box strong { color: #4fc3f7; }
        .info-box ol { margin: 8px 0 0 18px; padding: 0; }
        .btn-row { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
    </style>
</head>
<body>
    <nav class="navbar admin-navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo" translate="no"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text" translate="no">Wasla</span> <span class="admin-tag">Admin</span></a>
        <ul class="nav-links"><li><a href="dashboard.php">Dashboard</a></li><li><a href="users.php">Users</a></li><li><a href="projects.php">Projects</a></li><li><a href="reports.php">Reports</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Admin Panel</span><div class="user-avatar-small"><i class="fas fa-user-shield"></i></div></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar admin-sidebar">
            <div class="sidebar-profile"><div class="profile-avatar"><i class="fas fa-user-shield"></i></div><h3 class="profile-name">System<br>Admin</h3></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="users.php" class="sidebar-link"><i class="fas fa-users"></i><span>Users</span></a>
                <a href="projects.php" class="sidebar-link"><i class="fas fa-folder-open"></i><span>Projects</span></a>
                <a href="reports.php" class="sidebar-link"><i class="fas fa-flag"></i><span>Reports</span></a>
                <a href="email_settings.php" class="sidebar-link active"><i class="fas fa-envelope-open-text"></i><span>Email Settings</span></a>
            </nav>
            <div class="sidebar-footer"><button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <div class="page-header"><h1 class="section-title"><i class="fas fa-envelope-open-text" style="color:var(--accent)"></i> Email / SMTP Settings</h1></div>

            <?php if ($success): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Status -->
            <?php if ($settings && !empty($settings['smtp_username'])): ?>
                <div class="status-indicator status-ok"><i class="fas fa-circle"></i> SMTP is configured — emails will be sent to real inboxes.</div>
            <?php else: ?>
                <div class="status-indicator status-warn"><i class="fas fa-exclamation-triangle"></i> SMTP is NOT configured — verification codes won't be emailed yet.</div>
            <?php endif; ?>

            <!-- Instructions -->
            <div class="info-box">
                <strong><i class="fas fa-info-circle"></i> How to get Gmail credentials:</strong>
                <ol>
                    <li>Go to your Google Account → <strong>Security</strong></li>
                    <li>Enable <strong>2-Step Verification</strong></li>
                    <li>Go to <strong>App Passwords</strong> → Select "Mail" → Generate</li>
                    <li>Copy the 16-character password and paste it below</li>
                </ol>
            </div>

            <div class="email-card">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-server"></i> SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-plug"></i> SMTP Port</label>
                            <input type="number" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at"></i> Gmail Address (SMTP Username)</label>
                        <input type="email" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" placeholder="yourname@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key"></i> Gmail App Password</label>
                        <input type="password" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" placeholder="16-character App Password" required>
                        <p class="hint">Use a Gmail App Password — NOT your regular Gmail login password.</p>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> From Email</label>
                            <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp_from_email'] ?? 'noreply@wasla.com') ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-signature"></i> From Name</label>
                            <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp_from_name'] ?? 'Wasla') ?>">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="submit" name="save" class="btn-save"><i class="fas fa-save"></i> Save Settings</button>
                        <button type="submit" name="send_test" class="btn-test"><i class="fas fa-paper-plane"></i> Save & Send Test Email</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <footer class="footer"><div class="footer-left"><h3 translate="no">Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>
</body>
</html>
