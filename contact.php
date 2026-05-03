<?php
session_start();

// Prevent caching so the user's logged-in state is always fresh
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$is_logged_in = isset($_SESSION['user_id']);
$dashboard_link = 'index.php';
if ($is_logged_in) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'usher') {
        $dashboard_link = 'usher/dashboard.php';
    } else {
        $dashboard_link = 'dashboard.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Contact Us</title>
    <meta name="description" content="Get in touch with the Wasla team — we'd love to hear from you.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body class="landing-body">
    <nav class="landing-nav">
        <a href="<?php echo $dashboard_link; ?>" class="logo" translate="no">
            <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36">
            <span class="logo-text" translate="no">Wasla</span>
        </a>
        <div class="landing-nav-links">
            <a href="index.php#features">Features</a>
            <a href="contact.php" class="active">Contact</a>
        </div>
        <div class="landing-nav-actions">
            <?php if ($is_logged_in): ?>
                <a href="<?php echo $dashboard_link; ?>" class="btn-landing-login">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="btn-landing-login">Log In</a>
                <a href="signup.php" class="btn-landing-signup">Get Started</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="contact-page">
        <div class="landing-container">
            <div class="legal-header" style="padding-top:40px">
                <h1>Contact Us</h1>
                <p class="legal-updated">We'd love to hear from you — reach out and we'll get back within 24 hours.</p>
            </div>
            <div class="contact-grid">
                <!-- Contact Cards -->
                <div class="contact-info-col">
                    <div class="contact-info-card">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <h3>Email Us</h3>
                        <p>support@wasla.com</p>
                        <span class="contact-info-hint">We respond within 24 hours</span>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon contact-info-icon-green"><i class="fas fa-phone-alt"></i></div>
                        <h3>Call Us</h3>
                        <p>+201060037198</p>
                        <span class="contact-info-hint">Sun-Thu, 9AM-6PM (AST)</span>
                    </div>
                    <div class="contact-info-card">
                        <div class="contact-info-icon contact-info-icon-purple"><i class="fas fa-map-marker-alt"></i></div>
                        <h3>Visit Us</h3>
                        <p>badr university in cairo</p>
                        <span class="contact-info-hint">Cairo, Egypt</span>
                    </div>
                </div>
                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h2>Send Us a Message</h2>
                    <form class="contact-form" id="contact-form" onsubmit="return false">
                        <div class="form-row">
                            <div class="form-group"><label class="form-label">First Name *</label><input type="text" class="form-input" placeholder="John" required></div>
                            <div class="form-group"><label class="form-label">Last Name *</label><input type="text" class="form-input" placeholder="Doe" required></div>
                        </div>
                        <div class="form-group"><label class="form-label">Email Address *</label><input type="email" class="form-input" placeholder="john@example.com" required></div>
                        <div class="form-group"><label class="form-label">Subject</label>
                            <select class="form-input">
                                <option>General Inquiry</option>
                                <option>Partnership</option>
                                <option>Technical Support</option>
                                <option>Billing</option>
                                <option>Feature Request</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">Message *</label><textarea class="form-input form-textarea" rows="5" placeholder="Tell us how we can help..." required></textarea></div>
                        <button type="submit" class="btn-contact-submit" id="contact-submit">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Map Section -->
    <div class="contact-map-section">
        <div class="landing-container">
            <div class="contact-map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <p>Wasla Headquarters — badr university in cairo</p>
            </div>
        </div>
    </div>
    <footer class="landing-footer-simple">
        <p>&copy; 2024 Wasla Digital Conduit. All rights reserved.</p>
        <div class="landing-footer-bottom-links">
            <a href="terms.php">Terms</a>
            <a href="privacy.php">Privacy</a>
            <a href="contact.php">Contact</a>
        </div>
    </footer>
    <script>
        document.getElementById('contact-submit').addEventListener('click', function(){
            const btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Message Sent!';
                btn.style.background = 'var(--accent)';
                setTimeout(() => {
                    <?php if ($is_logged_in): ?>
                    window.location.href = '<?php echo $dashboard_link; ?>';
                    <?php else: ?>
                    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
                    btn.style.background = '';
                    <?php endif; ?>
                }, 1000);
            }, 1500);
        });
    </script>
</body>
</html>
