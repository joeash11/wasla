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
        <span class="welcome-text">Welcome <?php echo htmlspecialchars($user_name); ?></span>
        <a href="profile.php" class="user-avatar-small"><i class="fas fa-user-circle"></i></a>
    </div>
</nav>
