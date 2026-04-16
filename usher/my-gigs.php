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
    <title>Wasla - My Gigs</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
</head>
<body>
    <nav class="navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span></a>
        <ul class="nav-links"><li><a href="dashboard.php">Dashboard</a></li><li><a href="jobs.php">Available Jobs</a></li><li><a href="my-gigs.php" class="active">My Gigs</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Welcome <?php echo htmlspecialchars($user_name); ?></span><a href="profile.php" class="user-avatar-small"><i class="fas fa-user-circle"></i></a></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile"><a href="profile.php" class="profile-avatar"><i class="fas fa-user-circle"></i></a><h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3><span class="usher-badge"><i class="fas fa-id-badge"></i> Usher</span></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="jobs.php" class="sidebar-link"><i class="fas fa-search"></i><span>Available Jobs</span></a>
                <a href="my-gigs.php" class="sidebar-link active"><i class="fas fa-calendar-check"></i><span>My Gigs</span></a>
                <a href="profile.php" class="sidebar-link"><i class="fas fa-user"></i><span>Profile</span></a>
            </nav>
            <div class="sidebar-footer"><a href="../help.php" class="sidebar-link"><i class="fas fa-question-circle"></i><span>Help Center</span></a><button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <h1 class="section-title">My Gigs</h1>
            <!-- Earnings Summary -->
            <section class="stats-row" id="earnings-summary">
                <div class="stat-card"><p class="stat-label">This Month</p><h2 class="stat-value" id="sum-month">--</h2></div>
                <div class="stat-card"><p class="stat-label">Month Change</p><h2 class="stat-value" id="sum-change">--</h2></div>
                <div class="stat-card"><p class="stat-label">Gigs This Month</p><h2 class="stat-value" id="sum-gigs">--</h2></div>
                <div class="stat-card"><p class="stat-label">Avg Rating</p><h2 class="stat-value" id="sum-rating">--</h2></div>
            </section>
            <!-- Tab Filters -->
            <div class="gig-tabs">
                <button class="tab-btn active" data-filter="all">All</button>
                <button class="tab-btn" data-filter="upcoming">Upcoming</button>
                <button class="tab-btn" data-filter="completed">Completed</button>
                <button class="tab-btn" data-filter="cancelled">Cancelled</button>
            </div>
            <div class="gigs-list" id="gigs-list">
                <p style="color:var(--gray-400);text-align:center">Loading gigs...</p>
            </div>
        </main>
    </div>
    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>
    <script>
    let allGigs = [];
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`/wasla/api/usher_gigs.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const s = data.summary;
                document.getElementById('sum-month').textContent = 'SAR ' + Number(s.this_month_earnings).toLocaleString();
                document.getElementById('sum-change').innerHTML = (s.month_change_pct >= 0 ? '<span style="color:var(--accent)">↑ ' : '<span style="color:#ef4444">↓ ') + Math.abs(s.month_change_pct) + '%</span>';
                document.getElementById('sum-gigs').textContent = s.gigs_this_month;
                document.getElementById('sum-rating').textContent = s.avg_rating + ' / 5';

                allGigs = data.gigs;
                renderGigs(allGigs);
            });

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;
                renderGigs(filter === 'all' ? allGigs : allGigs.filter(g => g.status === filter));
            });
        });
    });

    function renderGigs(gigs) {
        const list = document.getElementById('gigs-list');
        if (gigs.length === 0) { list.innerHTML = '<p style="color:var(--gray-400);text-align:center">No gigs found</p>'; return; }
        list.innerHTML = gigs.map(g => {
            const statusClass = g.status === 'completed' ? 'status-completed' : g.status === 'cancelled' ? 'status-cancelled' : 'status-active';
            const statusLabel = g.status.charAt(0).toUpperCase() + g.status.slice(1);
            const ratingHtml = g.rating ? `<span class="gig-rating"><i class="fas fa-star"></i> ${g.rating}</span>` : '';
            return `
            <div class="gig-card">
                <div class="gig-date"><span class="gig-day">${g.day}</span><span class="gig-month">${g.month}</span></div>
                <div class="gig-info">
                    <div class="gig-title-row"><h3>${g.title}</h3><span class="gig-status ${statusClass}">${statusLabel}</span></div>
                    <div class="gig-meta">
                        <span><i class="fas fa-building"></i> ${g.company}</span>
                        <span><i class="fas fa-map-marker-alt"></i> ${g.location}</span>
                        <span><i class="fas fa-clock"></i> ${g.hours}h</span>
                    </div>
                </div>
                <div class="gig-pay-col">
                    <span class="gig-pay">SAR ${Number(g.pay).toLocaleString()}</span>
                    ${ratingHtml}
                </div>
            </div>`;
        }).join('');
    }
    </script>
</body>
</html>
