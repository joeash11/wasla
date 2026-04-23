<?php session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'usher') {
    header('Location: ../login.php');
    exit;
}
$usher_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : (isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'Usher');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Usher Dashboard</title>
    <meta name="description" content="Wasla Usher Dashboard - manage your gigs, earnings, and schedule.">
    <link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../wasla-theme.js?v=<?= time() ?>"></script>
</head>
<body>
    <?php $active_page = 'dashboard'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content">
            <h1 class="section-title">Usher Dashboard</h1>
            <!-- Stats -->
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Completed Projects</p><h2 class="stat-value" id="stat-gigs">--</h2></div>
                <div class="stat-card"><p class="stat-label">Total Earnings</p><h2 class="stat-value" id="stat-earnings">--</h2></div>
                <div class="stat-card"><p class="stat-label">Rating</p><h2 class="stat-value" id="stat-rating">--</h2></div>
                <div class="stat-card"><p class="stat-label">Upcoming Projects</p><h2 class="stat-value" id="stat-upcoming">--</h2></div>
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
                    <h3 class="panel-title"><i class="fas fa-calendar-alt"></i> Upcoming Projects</h3>
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
    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>

    <script>
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`../api/usher_dashboard.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) { console.error(data.error); return; }

                // Stats
                const s = data.stats;
                document.getElementById('stat-gigs').textContent = s.completed_gigs;
                document.getElementById('stat-earnings').textContent = 'EGP ' + Number(s.total_earnings).toLocaleString();
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
                    list.innerHTML = '<p style="color:var(--gray-400);text-align:center">No upcoming projects</p>';
                } else {
                    list.innerHTML = data.upcoming_gigs.map(g => `
                        <div class="upcoming-item">
                            <div class="upcoming-date"><span class="upcoming-day">${g.day}</span><span class="upcoming-month">${g.month}</span></div>
                            <div class="upcoming-info"><strong>${g.title}</strong><span><i class="fas fa-map-marker-alt"></i> ${g.location}</span></div>
                            <span class="upcoming-pay">EGP ${Number(g.pay).toLocaleString()}</span>
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
                            text = `Completed project at <strong>${a.detail}</strong>`;
                        } else if (a.type === 'payment') {
                            icon = 'fa-money-bill'; iconClass = 'activity-icon-blue';
                            text = `Received payment <strong>EGP ${Number(a.amount).toLocaleString()}</strong>`;
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
