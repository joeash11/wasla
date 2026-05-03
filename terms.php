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
    } else if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        $dashboard_link = 'admin/dashboard.php';
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
    <title>Wasla - Terms of Service</title>
    <meta name="description" content="Wasla Terms of Service - read our platform terms and conditions.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body class="landing-body">
    <nav class="landing-nav">
        <a href="index.php" class="logo" translate="no">
            <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36">
            <span class="logo-text" translate="no">Wasla</span>
        </a>
        <div class="landing-nav-links">
            <a href="index.php#features">Features</a>
            <a href="contact.php">Contact</a>
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
    <div class="legal-page">
        <div class="legal-container">
            <div class="legal-header">
                <h1>Terms of Service</h1>
                <p class="legal-updated">Last updated: April 1, 2026</p>
            </div>
            <div class="legal-content">
                <section class="legal-section">
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing or using the Wasla platform ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to all of these Terms, you may not use the Service. Wasla Digital Conduit ("Wasla", "we", "us", or "our") reserves the right to update these Terms at any time.</p>
                </section>
                <section class="legal-section">
                    <h2>2. Description of Service</h2>
                    <p>Wasla provides a digital platform that connects event managers with professional ushers, coordinators, and event staff across the MENA region. Our services include project management tools, messaging, talent matching, and event coordination features.</p>
                </section>
                <section class="legal-section">
                    <h2>3. User Accounts</h2>
                    <p>To use certain features of the Service, you must create an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to:</p>
                    <ul>
                        <li>Provide accurate and complete information during registration</li>
                        <li>Keep your account information up to date</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                        <li>Not share your account credentials with third parties</li>
                    </ul>
                </section>
                <section class="legal-section">
                    <h2>4. User Conduct</h2>
                    <p>You agree not to use the Service to:</p>
                    <ul>
                        <li>Violate any applicable laws or regulations</li>
                        <li>Post false, misleading, or fraudulent content</li>
                        <li>Harass, threaten, or discriminate against other users</li>
                        <li>Interfere with or disrupt the Service or its servers</li>
                        <li>Attempt to gain unauthorized access to any part of the Service</li>
                    </ul>
                </section>
                <section class="legal-section">
                    <h2>5. Payment Terms</h2>
                    <p>Certain features of the Service may require payment. All fees are quoted in Saudi Riyals (SAR) unless otherwise specified. Payments are processed securely through our third-party payment providers. Refund policies are detailed in our separate Refund Policy.</p>
                </section>
                <section class="legal-section">
                    <h2>6. Intellectual Property</h2>
                    <p>The Service and its original content, features, and functionality are owned by Wasla Digital Conduit and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
                </section>
                <section class="legal-section">
                    <h2>7. Limitation of Liability</h2>
                    <p>Wasla shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of or inability to use the Service. Our total liability shall not exceed the amount you paid to us in the twelve (12) months preceding the claim.</p>
                </section>
                <section class="legal-section">
                    <h2>8. Termination</h2>
                    <p>We may terminate or suspend your account at any time, with or without cause, with or without notice. Upon termination, your right to use the Service will immediately cease. All provisions of these Terms that by their nature should survive termination shall survive.</p>
                </section>
                <section class="legal-section">
                    <h2>9. Governing Law</h2>
                    <p>These Terms shall be governed by and construed in accordance with the laws of the Kingdom of Saudi Arabia, without regard to its conflict of law provisions.</p>
                </section>
                <section class="legal-section">
                    <h2>10. Contact Us</h2>
                    <p>If you have questions about these Terms, please contact us at <a href="mailto:legal@wasla.com">legal@wasla.com</a> or visit our <a href="contact.php">Contact page</a>.</p>
                </section>
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
</body>
</html>
