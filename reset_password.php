<?php
// ============================================
// Reset Password Page — Validates token & lets user set new password
// ============================================
require_once __DIR__ . '/db/connection.php';

$token = $_GET['token'] ?? '';
$valid = false;
$error = '';

if (empty($token)) {
    $error = 'Invalid or missing reset link.';
} else {
    $stmt = $conn->prepare("SELECT id, first_name, reset_expires FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $error = 'This reset link is invalid or has already been used.';
    } else {
        $user = $result->fetch_assoc();
        if (strtotime($user['reset_expires']) < time()) {
            $error = 'This reset link has expired. Please request a new one.';
        } else {
            $valid = true;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Reset Password</title>
    <meta name="description" content="Set a new password for your Wasla account.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body class="auth-body page-transition">
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="auth-left-content">
                <a href="index.php" class="logo" translate="no">
                    <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="48" height="48">
                    <span class="logo-text" style="color:#fff;font-size:1.6rem" translate="no">Wasla</span>
                </a>
                <h1 class="auth-hero-title">Set Your<br><span class="hero-gradient-text">New Password</span></h1>
                <p class="auth-hero-desc">Choose a strong password to keep your account secure.</p>
                <div class="auth-hero-stats">
                    <div class="auth-hero-stat"><i class="fas fa-lock"></i> Minimum 6 Characters</div>
                    <div class="auth-hero-stat"><i class="fas fa-key"></i> Choose Something Unique</div>
                    <div class="auth-hero-stat"><i class="fas fa-shield-alt"></i> Encrypted & Secure</div>
                </div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <?php if (!$valid): ?>
                    <h2 class="auth-form-title">Link Invalid</h2>
                    <div class="auth-error" style="display:flex">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                    <p class="auth-switch" style="margin-top:24px">
                        <a href="forgot_password.php">Request a new reset link</a>
                    </p>
                <?php else: ?>
                    <h2 class="auth-form-title">Create New Password</h2>
                    <p class="auth-form-subtitle">Hi <?= htmlspecialchars($user['first_name']) ?>, enter your new password below</p>

                    <div class="auth-error" id="auth-error" style="display:none">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="error-text"></span>
                    </div>
                    <div class="auth-success" id="auth-success" style="display:none">
                        <i class="fas fa-check-circle"></i>
                        <span id="success-text"></span>
                    </div>

                    <form class="auth-form" id="reset-form" onsubmit="handleReset(event)">
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" name="password"
                                    class="form-input auth-input" id="new-password" placeholder="Enter new password" required minlength="6">
                                <button type="button" class="auth-eye-btn" onclick="togglePassword('new-password',this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm Password</label>
                            <div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" name="confirm_password"
                                    class="form-input auth-input" id="confirm-password" placeholder="Confirm new password" required minlength="6">
                                <button type="button" class="auth-eye-btn" onclick="togglePassword('confirm-password',this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn-auth-submit" id="reset-submit">
                            <i class="fas fa-check-circle"></i> Update Password
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
            else { input.type = 'password'; icon.className = 'fas fa-eye'; }
        }

        async function handleReset(e) {
            e.preventDefault();
            const btn = document.getElementById('reset-submit');
            const pw = document.getElementById('new-password').value;
            const cpw = document.getElementById('confirm-password').value;
            const errDiv = document.getElementById('auth-error');
            const sucDiv = document.getElementById('auth-success');

            errDiv.style.display = 'none';
            sucDiv.style.display = 'none';

            if (pw !== cpw) {
                errDiv.style.display = 'flex';
                document.getElementById('error-text').textContent = 'Passwords do not match.';
                return;
            }
            if (pw.length < 6) {
                errDiv.style.display = 'flex';
                document.getElementById('error-text').textContent = 'Password must be at least 6 characters.';
                return;
            }

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            btn.disabled = true;

            try {
                const res = await fetch('api/process_reset.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ token: '<?= htmlspecialchars($token) ?>', password: pw })
                });
                const data = await res.json();
                if (data.success) {
                    sucDiv.style.display = 'flex';
                    document.getElementById('success-text').textContent = 'Password updated! Redirecting to login...';
                    btn.innerHTML = '<i class="fas fa-check"></i> Done!';
                    setTimeout(() => window.location.href = 'login.php', 2000);
                } else {
                    errDiv.style.display = 'flex';
                    document.getElementById('error-text').textContent = data.error;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Update Password';
                    btn.disabled = false;
                }
            } catch (err) {
                errDiv.style.display = 'flex';
                document.getElementById('error-text').textContent = 'Network error. Please try again.';
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Update Password';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
