<?php require_once __DIR__ . '/admin_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla Admin - Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
</head>
<body>
    <nav class="navbar admin-navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span> <span class="admin-tag">Admin</span></a>
        <ul class="nav-links"><li><a href="dashboard.php" class="active">Dashboard</a></li><li><a href="users.php">Users</a></li><li><a href="projects.php">Projects</a></li><li><a href="reports.php">Reports</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Admin Panel</span><div class="user-avatar-small"><i class="fas fa-user-shield"></i></div></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar admin-sidebar">
            <div class="sidebar-profile"><div class="profile-avatar"><i class="fas fa-user-shield"></i></div><h3 class="profile-name">System<br>Admin</h3></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link active"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="users.php" class="sidebar-link"><i class="fas fa-users"></i><span>Users</span></a>
                <a href="projects.php" class="sidebar-link"><i class="fas fa-folder-open"></i><span>Projects</span></a>
                <a href="reports.php" class="sidebar-link"><i class="fas fa-flag"></i><span>Reports</span></a>
            </nav>
            <div class="sidebar-footer"><button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <h1 class="section-title">Admin Dashboard</h1>
            <!-- Platform Stats -->
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Total Users</p><h2 class="stat-value">1,247</h2></div>
                <div class="stat-card"><p class="stat-label">Active Projects</p><h2 class="stat-value">38</h2></div>
                <div class="stat-card"><p class="stat-label">Platform Revenue</p><h2 class="stat-value">SAR 245K</h2></div>
                <div class="stat-card"><p class="stat-label">New Signups (30d)</p><h2 class="stat-value">89</h2></div>
            </section>
            <!-- Revenue + User Growth -->
            <section class="revenue-row">
                <div class="revenue-card"><div class="revenue-card-icon revenue-icon-green"><i class="fas fa-users"></i></div><div class="revenue-card-info"><p class="revenue-label">Clients</p><h3 class="revenue-value">342</h3><span class="revenue-trend trend-up"><i class="fas fa-arrow-up"></i> 14 new this week</span></div></div>
                <div class="revenue-card"><div class="revenue-card-icon revenue-icon-blue"><i class="fas fa-id-badge"></i></div><div class="revenue-card-info"><p class="revenue-label">Ushers</p><h3 class="revenue-value">905</h3><span class="revenue-trend trend-up"><i class="fas fa-arrow-up"></i> 32 new this week</span></div></div>
                <div class="revenue-card"><div class="revenue-card-icon revenue-icon-orange"><i class="fas fa-exclamation-triangle"></i></div><div class="revenue-card-info"><p class="revenue-label">Pending Approvals</p><h3 class="revenue-value">7</h3><span class="revenue-trend trend-neutral"><i class="fas fa-minus"></i> Requires review</span></div></div>
                <div class="revenue-card"><div class="revenue-card-icon revenue-icon-purple"><i class="fas fa-flag"></i></div><div class="revenue-card-info"><p class="revenue-label">Flagged Content</p><h3 class="revenue-value">3</h3><span class="revenue-trend trend-down"><i class="fas fa-arrow-down"></i> 2 less than last week</span></div></div>
            </section>
            <!-- Charts + Activity -->
            <section class="dashboard-grid-2col">
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-chart-bar"></i> User Growth (6 Months)</h3>
                    <div class="chart-container">
                        <div class="bar-chart">
                            <div class="bar-group"><div class="bar" style="height:35%"></div><span>Jan</span></div>
                            <div class="bar-group"><div class="bar" style="height:48%"></div><span>Feb</span></div>
                            <div class="bar-group"><div class="bar" style="height:55%"></div><span>Mar</span></div>
                            <div class="bar-group"><div class="bar" style="height:68%"></div><span>Apr</span></div>
                            <div class="bar-group"><div class="bar" style="height:78%"></div><span>May</span></div>
                            <div class="bar-group"><div class="bar bar-accent" style="height:92%"></div><span>Jun</span></div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-clock"></i> Recent Activity</h3>
                    <div class="activity-list">
                        <div class="activity-item"><div class="activity-icon activity-icon-green"><i class="fas fa-user-plus"></i></div><div><span class="activity-text">New client registered: <strong>Event Solutions LLC</strong></span><span class="activity-time">5 min ago</span></div></div>
                        <div class="activity-item"><div class="activity-icon activity-icon-blue"><i class="fas fa-folder-plus"></i></div><div><span class="activity-text">New project created: <strong>National Day Festival</strong></span><span class="activity-time">22 min ago</span></div></div>
                        <div class="activity-item"><div class="activity-icon activity-icon-orange"><i class="fas fa-flag"></i></div><div><span class="activity-text">Content flagged in project: <strong>Private Event #129</strong></span><span class="activity-time">1 hour ago</span></div></div>
                        <div class="activity-item"><div class="activity-icon activity-icon-purple"><i class="fas fa-check-circle"></i></div><div><span class="activity-text">Usher verified: <strong>Fatimah Al-Saud</strong></span><span class="activity-time">2 hours ago</span></div></div>
                        <div class="activity-item"><div class="activity-icon activity-icon-green"><i class="fas fa-money-bill"></i></div><div><span class="activity-text">Payment processed: <strong>SAR 12,500</strong></span><span class="activity-time">3 hours ago</span></div></div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>
</body>
</html>
