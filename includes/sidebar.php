<?php
// ============================================
// Shared Sidebar for Client Pages
// Requires client_guard.php to be included first
// $active_page should be set before including this file
// e.g. $active_page = 'dashboard';
// ============================================
$name_parts = explode(' ', $user_name);
$sidebar_first = $name_parts[0] ?? '';
$sidebar_last = isset($name_parts[1]) ? $name_parts[1] : '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <a href="profile.php" class="profile-avatar"><i class="fas fa-user-circle"></i></a>
        <h3 class="profile-name"><?php echo htmlspecialchars($sidebar_first); ?><br><?php echo htmlspecialchars($sidebar_last); ?></h3>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link<?php echo ($active_page ?? '') === 'dashboard' ? ' active' : ''; ?>">
            <i class="fas fa-th-large"></i><span>Dashboard</span>
        </a>
        <a href="projects.php" class="sidebar-link<?php echo ($active_page ?? '') === 'projects' ? ' active' : ''; ?>">
            <i class="fas fa-file-alt"></i><span>My Projects</span>
        </a>
        <a href="messages.php" class="sidebar-link<?php echo ($active_page ?? '') === 'messages' ? ' active' : ''; ?>">
            <i class="fas fa-envelope"></i><span>Messages</span>
        </a>
        <a href="settings.php" class="sidebar-link<?php echo ($active_page ?? '') === 'settings' ? ' active' : ''; ?>">
            <i class="fas fa-cog"></i><span>Settings</span>
        </a>
        <a href="checkout.php" class="sidebar-link<?php echo ($active_page ?? '') === 'checkout' ? ' active' : ''; ?>">
            <i class="fas fa-wallet"></i><span>Deposit Funds</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="help.php" class="sidebar-link"><i class="fas fa-question-circle"></i><span>Help Center</span></a>
        <a href="contact.php" class="sidebar-link"><i class="fas fa-envelope"></i><span>Contact Us</span></a>
        <button class="btn-logout" onclick="window.location.href='auth_logout.php'">Log Out</button>
    </div>
</aside>
