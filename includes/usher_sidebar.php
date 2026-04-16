<?php
// ============================================
// Shared Sidebar for Usher Pages
// Requires usher_guard.php to be included first
// $active_page should be set before including this file
// ============================================
$name_parts = explode(' ', $user_name);
$sidebar_first = $name_parts[0] ?? '';
$sidebar_last = isset($name_parts[1]) ? $name_parts[1] : '';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-profile">
        <a href="profile.php" class="profile-avatar"><i class="fas fa-user-circle"></i></a>
        <h3 class="profile-name"><?php echo htmlspecialchars($sidebar_first); ?><br><?php echo htmlspecialchars($sidebar_last); ?></h3>
        <span class="usher-badge"><i class="fas fa-id-badge"></i> Usher</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link<?php echo ($active_page ?? '') === 'dashboard' ? ' active' : ''; ?>">
            <i class="fas fa-th-large"></i><span>Dashboard</span>
        </a>
        <a href="jobs.php" class="sidebar-link<?php echo ($active_page ?? '') === 'jobs' ? ' active' : ''; ?>">
            <i class="fas fa-search"></i><span>Available Jobs</span>
        </a>
        <a href="my-gigs.php" class="sidebar-link<?php echo ($active_page ?? '') === 'my-gigs' ? ' active' : ''; ?>">
            <i class="fas fa-calendar-check"></i><span>My Gigs</span>
        </a>
        <a href="profile.php" class="sidebar-link<?php echo ($active_page ?? '') === 'profile' ? ' active' : ''; ?>">
            <i class="fas fa-user"></i><span>Profile</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../help.php" class="sidebar-link"><i class="fas fa-question-circle"></i><span>Help Center</span></a>
        <a href="../contact.php" class="sidebar-link"><i class="fas fa-envelope"></i><span>Contact Us</span></a>
        <button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button>
    </div>
</aside>
