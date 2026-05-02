<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Forgot Password</title>
    <meta name="description" content="Reset your Wasla account password securely.">
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
                <h1 class="auth-hero-title">Forgot Your<br>Password? <span class="hero-gradient-text">No Worries</span></h1>
                <p class="auth-hero-desc">Enter your email and we'll send you a secure link to reset your password instantly.</p>
                <div class="auth-hero-stats">
                    <div class="auth-hero-stat"><i class="fas fa-shield-alt"></i> Secure Reset Process</div>
                    <div class="auth-hero-stat"><i class="fas fa-clock"></i> Link Expires in 30 Minutes</div>
                    <div class="auth-hero-stat"><i class="fas fa-envelope"></i> Check Your Inbox</div>
                </div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <h2 class="auth-form-title">Reset Password</h2>
                <p class="auth-form-subtitle">Enter the email address associated with your account</p>

                <!-- Status Messages -->
                <div class="auth-error" id="auth-error" style="display:none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-text"></span>
                </div>
                <div class="auth-success" id="auth-success" style="display:none">
                    <i class="fas fa-check-circle"></i>
                    <span id="success-text"></span>
                </div>

                <form class="auth-form" id="forgot-form" onsubmit="handleForgot(event)">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="auth-input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email"
                                class="form-input auth-input" placeholder="Enter your email" required id="forgot-email"></div>
                    </div>
                    <button type="submit" class="btn-auth-submit" id="forgot-submit">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>
                <p class="auth-switch" style="margin-top:24px">
                    Remember your password? <a href="login.php">Log in</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        async function handleForgot(e) {
            e.preventDefault();
            const btn = document.getElementById('forgot-submit');
            const email = document.getElementById('forgot-email').value;
            const errDiv = document.getElementById('auth-error');
            const sucDiv = document.getElementById('auth-success');

            errDiv.style.display = 'none';
            sucDiv.style.display = 'none';
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;

            try {
                const res = await fetch('api/send_reset.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                if (data.success) {
                    sucDiv.style.display = 'flex';
                    document.getElementById('success-text').textContent = data.message;
                    btn.innerHTML = '<i class="fas fa-check"></i> Email Sent!';
                } else {
                    errDiv.style.display = 'flex';
                    document.getElementById('error-text').textContent = data.error;
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reset Link';
                    btn.disabled = false;
                }
            } catch (err) {
                errDiv.style.display = 'flex';
                document.getElementById('error-text').textContent = 'Network error. Please try again.';
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Reset Link';
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
