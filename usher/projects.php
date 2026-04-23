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
    <title>Wasla - My Projects</title>
    <link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../wasla-theme.js"></script>
</head>
<body>
    <?php $active_page = 'projects'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content">
            <h1 class="section-title">My Projects</h1>
            <!-- Earnings Summary -->
            <section class="stats-row" id="earnings-summary">
                <div class="stat-card"><p class="stat-label">This Month</p><h2 class="stat-value" id="sum-month">--</h2></div>
                <div class="stat-card"><p class="stat-label">Month Change</p><h2 class="stat-value" id="sum-change">--</h2></div>
                <div class="stat-card"><p class="stat-label">Projects This Month</p><h2 class="stat-value" id="sum-gigs">--</h2></div>
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
                <p style="color:var(--gray-400);text-align:center">Loading projects...</p>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
    let allGigs = [];
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`../api/usher_gigs.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const s = data.summary;
                document.getElementById('sum-month').textContent = 'EGP ' + Number(s.this_month_earnings).toLocaleString();
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
        if (gigs.length === 0) { list.innerHTML = '<p style="color:var(--gray-400);text-align:center">No projects found</p>'; return; }
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
                    <span class="gig-pay">EGP ${Number(g.pay).toLocaleString()}</span>
                    ${ratingHtml}
                </div>
            </div>`;
        }).join('');
    }
    </script>
</body>
</html>
