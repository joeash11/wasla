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
    <title>Wasla - Available Jobs</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
</head>
<body>
    <nav class="navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span></a>
        <ul class="nav-links"><li><a href="dashboard.php">Dashboard</a></li><li><a href="jobs.php" class="active">Available Jobs</a></li><li><a href="my-gigs.php">My Gigs</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Welcome <?php echo htmlspecialchars($user_name); ?></span><a href="profile.php" class="user-avatar-small"><i class="fas fa-user-circle"></i></a></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar">
            <div class="sidebar-profile"><a href="profile.php" class="profile-avatar"><i class="fas fa-user-circle"></i></a><h3 class="profile-name"><?php echo htmlspecialchars($user_name); ?></h3><span class="usher-badge"><i class="fas fa-id-badge"></i> Usher</span></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="jobs.php" class="sidebar-link active"><i class="fas fa-search"></i><span>Available Jobs</span></a>
                <a href="my-gigs.php" class="sidebar-link"><i class="fas fa-calendar-check"></i><span>My Gigs</span></a>
                <a href="profile.php" class="sidebar-link"><i class="fas fa-user"></i><span>Profile</span></a>
            </nav>
            <div class="sidebar-footer"><a href="../help.php" class="sidebar-link"><i class="fas fa-question-circle"></i><span>Help Center</span></a><button class="btn-logout" onclick="window.location.href='../api/logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <div class="page-header"><h1 class="section-title">Available Jobs</h1></div>
            <section class="filters-row">
                <div class="filter-search"><i class="fas fa-search"></i><input type="text" placeholder="Search jobs..." id="job-search"></div>
                <div class="filter-select"><select id="filter-location"><option value="">Location</option><option>Riyadh</option><option>Jeddah</option><option>Cairo</option><option>Dubai</option></select></div>
                <div class="filter-select"><select id="filter-pay"><option value="">Pay Range</option><option value="200-400">SAR 200-400</option><option value="400-600">SAR 400-600</option><option value="600+">SAR 600+</option></select></div>
            </section>
            <div class="projects-grid" id="jobs-grid">
                <p style="color:var(--gray-400);text-align:center;grid-column:1/-1">Loading jobs...</p>
            </div>
        </main>
    </div>
    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>
    <script>
    let allJobs = [];
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`/wasla/api/usher_jobs.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                allJobs = data.jobs;
                renderJobs(allJobs);
            });
    });

    function renderJobs(jobs) {
        const grid = document.getElementById('jobs-grid');
        if (jobs.length === 0) {
            grid.innerHTML = '<p style="color:var(--gray-400);text-align:center;grid-column:1/-1">No jobs found</p>';
            return;
        }
        grid.innerHTML = jobs.map(j => {
            const badge = j.status === 'active' ? '<div class="card-badge status-active">Hiring</div>' : '<div class="card-badge status-pending">Coming Soon</div>';
            const applied = j.already_applied;
            let btnHtml = '';
            if (applied === 'accepted' || applied === 'completed') {
                btnHtml = '<button class="btn-manage" style="background:var(--accent);pointer-events:none">✓ Accepted</button>';
            } else if (applied === 'pending') {
                btnHtml = '<button class="btn-manage" style="opacity:0.7;pointer-events:none">Pending...</button>';
            } else {
                btnHtml = `<button class="btn-manage" onclick="applyJob(this, ${j.id})">Apply Now</button>`;
            }
            return `
                <div class="project-card" data-location="${j.location}" data-pay="${j.pay}">
                    ${badge}
                    <div class="card-header"><h3>${j.title}</h3><p class="card-company"><i class="fas fa-building"></i> ${j.company}</p></div>
                    <div class="card-body">
                        <div class="card-detail"><i class="fas fa-map-marker-alt"></i><span>${j.location}</span></div>
                        <div class="card-detail"><i class="fas fa-calendar"></i><span>${j.date}</span></div>
                        <div class="card-detail"><i class="fas fa-clock"></i><span>${j.hours} hours</span></div>
                        <div class="card-detail"><i class="fas fa-money-bill"></i><span>SAR ${j.pay}/day</span></div>
                    </div>
                    <div class="card-tags"><span class="card-tag">${j.category || 'Event'}</span></div>
                    <div class="card-footer">
                        <span class="card-slots"><i class="fas fa-users"></i> ${j.slots_left} slots left</span>
                        ${btnHtml}
                    </div>
                </div>`;
        }).join('');
    }

    function applyJob(btn, projectId) {
        btn.textContent = 'Applying...';
        btn.style.opacity = '0.7';
        fetch('/wasla/api/apply_job.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: projectId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Applied!';
                btn.style.background = 'var(--accent)';
                btn.style.pointerEvents = 'none';
            } else {
                btn.textContent = data.error || 'Error';
                btn.style.opacity = '1';
            }
        });
    }

    // Filters
    document.getElementById('job-search').addEventListener('input', filterJobs);
    document.getElementById('filter-location').addEventListener('change', filterJobs);
    document.getElementById('filter-pay').addEventListener('change', filterJobs);

    function filterJobs() {
        const q = document.getElementById('job-search').value.toLowerCase();
        const loc = document.getElementById('filter-location').value;
        const pay = document.getElementById('filter-pay').value;
        let filtered = allJobs.filter(j => {
            if (q && !j.title.toLowerCase().includes(q) && !j.company.toLowerCase().includes(q)) return false;
            if (loc && !j.location.includes(loc)) return false;
            if (pay) {
                if (pay === '200-400' && (j.pay < 200 || j.pay > 400)) return false;
                if (pay === '400-600' && (j.pay < 400 || j.pay > 600)) return false;
                if (pay === '600+' && j.pay < 600) return false;
            }
            return true;
        });
        renderJobs(filtered);
    }
    </script>
</body>
</html>
