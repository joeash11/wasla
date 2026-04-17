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
    <title>Wasla - Available Projects</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../theme-init.js"></script>
</head>
<body>
    <?php $active_page = 'jobs'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content">
            <div class="page-header"><h1 class="section-title">Available Projects</h1></div>
            <section class="filters-row">
                <div class="filter-search"><i class="fas fa-search"></i><input type="text" placeholder="Search projects..." id="job-search"></div>
                <div class="filter-select"><select id="filter-location">
                    <option value="">Location</option>
                    <option>Cairo</option>
                    <option>Alexandria</option>
                    <option>Jeddah</option>
                    <option>Dubai</option>
                    <option>Sharm Elsheikh</option>
                    <option>Riyadh</option>
                    <option>North Coast</option>
                    <option>El Gouna</option>
                </select></div>
                <div class="filter-select"><select id="filter-pay"><option value="">Pay Range</option><option value="200-400">EGP 200-400</option><option value="400-600">EGP 400-600</option><option value="600+">EGP 600+</option></select></div>
            </section>
            <div class="projects-grid" id="jobs-grid">
                <p style="color:var(--gray-400);text-align:center;grid-column:1/-1">Loading projects...</p>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
    let allJobs = [];
    const USHER_ID = <?php echo $usher_id; ?>;

    document.addEventListener('DOMContentLoaded', () => {
        fetch(`../api/usher_jobs.php?usher_id=${USHER_ID}`)
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
            grid.innerHTML = '<p style="color:var(--gray-400);text-align:center;grid-column:1/-1">No projects found</p>';
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
                    <div class="card-header"><h3 style="font-weight:700; font-size:1.25rem; color:var(--primary); font-family: 'Inter', sans-serif;">${j.title}</h3><p class="card-company" style="font-weight:500; margin-top:4px;"><i class="fas fa-building"></i> ${j.company}</p></div>
                    <div class="card-body">
                        <div class="card-detail"><i class="fas fa-map-marker-alt"></i><span>${j.location}</span></div>
                        <div class="card-detail"><i class="fas fa-calendar"></i><span>${j.date}</span></div>
                        <div class="card-detail"><i class="fas fa-clock"></i><span>${j.hours} hours</span></div>
                        <div class="card-detail"><i class="fas fa-money-bill"></i><span>EGP ${j.pay}/day</span></div>
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
        fetch('../api/apply_job.php', {
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
