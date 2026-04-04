<?php session_start();
$usher_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 3;
$user_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Ahmed';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Usher Dashboard</title>
    <meta name="description" content="Wasla Usher Dashboard - manage your gigs, earnings, and schedule.">
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
</head>
<body>
    <nav class="navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span></a>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="jobs.php">Available Jobs</a></li>
            <li><a href="my-gigs.php">My Gigs</a></li>
        </ul>
    </div><div class="navbar-right">
        <span class="welcome-text">Welcome <?php echo htmlspecialchars($user_name); ?></span>
        <a href="profile.php" class="user-avatar-small"><i class="fas fa-user-circle"></i></a>
    </div></nav>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile"><a href="profile.php" class="profile-avatar"><i class="fas fa-user-circle"></i></a><h3 class="profile-name" id="sidebar-name">Loading...</h3><span class="usher-badge"><i class="fas fa-id-badge"></i> Usher</span></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link active"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="jobs.php" class="sidebar-link"><i class="fas fa-search"></i><span>Available Jobs</span></a>
                <a href="my-gigs.php" class="sidebar-link"><i class="fas fa-calendar-check"></i><span>My Gigs</span></a>
                <a href="profile.php" class="sidebar-link"><i class="fas fa-user"></i><span>Profile</span></a>
            </nav>
            <div class="sidebar-footer">
                <a href="../help.html" class="sidebar-link"><i class="fas fa-question-circle"></i><span>Help Center</span></a>
                <button class="btn-logout" onclick="window.location.href='../api/logout.php'">Log Out</button>
            </div>
        </aside>
        <main class="content">
            <h1 class="section-title">Usher Dashboard</h1>
            <!-- Stats -->
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Completed Gigs</p><h2 class="stat-value" id="stat-gigs">--</h2></div>
                <div class="stat-card"><p class="stat-label">Total Earnings</p><h2 class="stat-value" id="stat-earnings">--</h2></div>
                <div class="stat-card"><p class="stat-label">Rating</p><h2 class="stat-value" id="stat-rating">--</h2></div>
                <div class="stat-card"><p class="stat-label">Upcoming Shifts</p><h2 class="stat-value" id="stat-upcoming">--</h2></div>
            </section>
            <!-- Revenue + Upcoming -->
            <section class="dashboard-grid-2col">
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-chart-bar"></i> Monthly Earnings</h3>
                    <div class="chart-container">
                        <div class="bar-chart" id="earnings-chart">
                            <p style="color:var(--gray-400);text-align:center;width:100%">Loading chart...</p>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-calendar-alt"></i> Upcoming Gigs</h3>
                    <div class="upcoming-list" id="upcoming-list">
                        <p style="color:var(--gray-400);text-align:center">Loading...</p>
                    </div>
                </div>
            </section>
            <!-- Recent Activity -->
            <section class="dashboard-panel" style="margin-top:24px">
                <h3 class="panel-title"><i class="fas fa-clock"></i> Recent Activity</h3>
                <div class="activity-list" id="activity-list">
                    <p style="color:var(--gray-400);text-align:center">Loading...</p>
                </div>
            </section>
        </main>
    </div>
    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.html">TERMS OF SERVICE</a><a href="../privacy.html">PRIVACY POLICY</a><a href="../contact.html">CONTACT US</a></div></footer>

    <script>
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`/wasla/api/usher_dashboard.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) { console.error(data.error); return; }

                // User info
                const u = data.user;
                document.getElementById('sidebar-name').innerHTML = `${u.first_name}<br>${u.last_name}`;

                // Stats
                const s = data.stats;
                document.getElementById('stat-gigs').textContent = s.completed_gigs;
                document.getElementById('stat-earnings').textContent = 'SAR ' + Number(s.total_earnings).toLocaleString();
                document.getElementById('stat-rating').innerHTML = `${s.avg_rating} <small style="font-size:0.6em;color:var(--gray-400)">/ 5</small>`;
                document.getElementById('stat-upcoming').textContent = s.upcoming_shifts;

                // Bar chart
                const chart = document.getElementById('earnings-chart');
                if (data.monthly_earnings.length === 0) {
                    chart.innerHTML = '<p style="color:var(--gray-400);text-align:center;width:100%">No earnings data yet</p>';
                } else {
                    chart.innerHTML = data.monthly_earnings.map((m, i) => {
                        const isLast = i === data.monthly_earnings.length - 1;
                        return `<div class="bar-group"><div class="bar ${isLast ? 'bar-accent' : ''}" style="height:${m.percentage}%"></div><span>${m.month}</span></div>`;
                    }).join('');
                }

                // Upcoming gigs
                const list = document.getElementById('upcoming-list');
                if (data.upcoming_gigs.length === 0) {
                    list.innerHTML = '<p style="color:var(--gray-400);text-align:center">No upcoming gigs</p>';
                } else {
                    list.innerHTML = data.upcoming_gigs.map(g => `
                        <div class="upcoming-item">
                            <div class="upcoming-date"><span class="upcoming-day">${g.day}</span><span class="upcoming-month">${g.month}</span></div>
                            <div class="upcoming-info"><strong>${g.title}</strong><span><i class="fas fa-map-marker-alt"></i> ${g.location}</span></div>
                            <span class="upcoming-pay">SAR ${Number(g.pay).toLocaleString()}</span>
                        </div>
                    `).join('');
                }

                // Activity
                const actList = document.getElementById('activity-list');
                if (data.recent_activity.length === 0) {
                    actList.innerHTML = '<p style="color:var(--gray-400);text-align:center">No recent activity</p>';
                } else {
                    actList.innerHTML = data.recent_activity.map(a => {
                        let icon, iconClass, text;
                        if (a.type === 'gig_completed') {
                            icon = 'fa-check'; iconClass = 'activity-icon-green';
                            text = `Completed gig at <strong>${a.detail}</strong>`;
                        } else if (a.type === 'payment') {
                            icon = 'fa-money-bill'; iconClass = 'activity-icon-blue';
                            text = `Received payment <strong>SAR ${Number(a.amount).toLocaleString()}</strong>`;
                        } else if (a.type === 'review') {
                            icon = 'fa-star'; iconClass = 'activity-icon-purple';
                            text = `New ${a.amount}-star review from <strong>${a.detail}</strong>`;
                        }
                        return `<div class="activity-item"><div class="activity-icon ${iconClass}"><i class="fas ${icon}"></i></div><div><span class="activity-text">${text}</span><span class="activity-time">${a.time_ago}</span></div></div>`;
                    }).join('');
                }
            })
            .catch(err => console.error('Dashboard API error:', err));
    });
    </script>
</body>
</html>
