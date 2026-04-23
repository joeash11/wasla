<?php
// ============================================
// Shared Navbar for Usher Pages
// Requires usher_guard.php to be included first
// ============================================
?>
<nav class="navbar" id="navbar">
    <div class="navbar-left">
        <a href="dashboard.php" class="logo">
            <img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36">
            <span class="logo-text">Wasla</span>
        </a>
    </div>
    <div class="navbar-right">
        <span class="welcome-text"><span>Welcome</span> <?php echo htmlspecialchars($user_name); ?></span>
        <!-- Language Toggle -->
        <button class="lang-toggle-btn" id="lang-toggle-btn" onclick="toggleLanguage()" title="Switch Language">
            <i class="fas fa-globe"></i>
            <span id="lang-label"><?php echo (isset($_COOKIE['wasla_lang']) && $_COOKIE['wasla_lang'] === 'ar') ? 'EN' : 'AR'; ?></span>
        </button>
        <a href="profile.php" class="user-avatar-small"><i class="fas fa-user-circle"></i></a>
    </div>
</nav>
<script>
function toggleLanguage() {
    const current = localStorage.getItem('wasla_language') || 'en';
    const next = current === 'en' ? 'ar' : 'en';
    if (window.waslaSetLanguage) {
        window.waslaSetLanguage(next);
    } else {
        localStorage.setItem('wasla_language', next);
        location.reload();
    }
}
// Set correct label on load
(function() {
    const lbl = document.getElementById('lang-label');
    if (lbl) lbl.textContent = (localStorage.getItem('wasla_language') === 'ar') ? 'EN' : 'AR';
})();
</script>
