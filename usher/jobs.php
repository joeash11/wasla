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
    <link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../wasla-theme.js?v=<?= time() ?>"></script>
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

            <!-- Project Details Modal -->
            <div id="project-modal" class="modal-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
                <div class="modal-content" style="background:var(--white);width:90%;max-width:600px;border-radius:16px;padding:32px;position:relative;box-shadow:var(--shadow-xl);">
                    <button class="modal-close" onclick="closeProjectModal()" style="position:absolute;top:20px;right:20px;background:none;border:none;font-size:1.2rem;color:var(--gray-400);cursor:pointer;"><i class="fas fa-times"></i></button>
                    <div style="margin-bottom:24px;">
                        <div id="modal-badge" style="margin-bottom:12px;"></div>
                        <h2 id="modal-title" style="color:var(--primary);font-size:1.5rem;font-weight:800;margin-bottom:8px;">Project Title</h2>
                        <p id="modal-company" style="color:var(--gray-600);font-weight:500;margin-bottom:16px;"><i class="fas fa-building"></i> Company Name</p>
                        
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;background:var(--gray-50);padding:16px;border-radius:12px;border:1px solid var(--gray-200);">
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Location</span><strong id="modal-location" style="color:var(--gray-800);">Location</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Date</span><strong id="modal-date" style="color:var(--gray-800);">Date</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Pay</span><strong id="modal-pay" style="color:var(--accent);">EGP 0/day</strong></div>
                            <div><span style="color:var(--gray-500);font-size:0.85rem;display:block;margin-bottom:4px;">Availability</span><strong id="modal-slots" style="color:var(--gray-800);">0 slots left</strong></div>
                        </div>

                        <h3 style="color:var(--primary);font-size:1.1rem;margin-bottom:8px;">Project Description</h3>
                        <p id="modal-description" style="color:var(--gray-600);line-height:1.6;font-size:0.95rem;margin-bottom:24px;white-space:pre-wrap;">Description goes here.</p>
                        
                        <div id="modal-action-container" style="display:flex;justify-content:flex-end;gap:12px;">
                            <button onclick="closeProjectModal()" style="padding:12px 24px;background:var(--gray-200);color:var(--gray-800);border:none;border-radius:8px;font-weight:600;cursor:pointer;">Close</button>
                            <button id="modal-apply-btn" style="padding:12px 24px;background:var(--accent);color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;box-shadow:0 4px 12px var(--accent-glow);">Apply Now</button>
                        </div>
                    </div>
                </div>
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
            let statusText = '';
            let btnClass = '';
            if (applied === 'accepted' || applied === 'completed') {
                statusText = '✓ Accepted';
                btnClass = 'btn-manage';
            } else if (applied === 'pending') {
                statusText = 'Pending...';
                btnClass = 'btn-manage';
            }
            
            const actionBtn = applied 
                ? `<button class="${btnClass}" style="background:var(--gray-200);color:var(--gray-600);pointer-events:none">${statusText}</button>`
                : `<button class="btn-manage" onclick="openProjectModal(${j.id})">Review & Apply</button>`;

            return `
                <div class="project-card" data-location="${j.location}" data-pay="${j.pay}">
                    ${badge}
                    <div class="card-header"><h3 style="font-weight:700; font-size:1.25rem; color:var(--primary); font-family: 'Inter', sans-serif;">${j.title}</h3><p class="card-company" style="font-weight:500; margin-top:4px;"><i class="fas fa-building"></i> ${j.company}</p></div>
                    <div class="card-body">
                        <div class="card-detail"><i class="fas fa-map-marker-alt"></i><span class="loc-text">${j.location}</span></div>
                        <div class="card-detail"><i class="fas fa-calendar"></i><span>${j.date}</span></div>
                        <div class="card-detail"><i class="fas fa-clock"></i><span><span>${j.hours}</span> <span>hours</span></span></div>
                        <div class="card-detail"><i class="fas fa-money-bill"></i><span><span>EGP</span> <span>${j.pay}</span><span>/day</span></span></div>
                    </div>
                    <div class="card-tags"><span class="card-tag">${j.category || 'Event'}</span></div>
                    <div class="card-footer">
                        <span class="card-slots"><i class="fas fa-users"></i> <span>${j.slots_left}</span> <span>slots left</span></span>
                        ${actionBtn}
                    </div>
                </div>`;
        }).join('');
    }

    function openProjectModal(id) {
        const j = allJobs.find(job => job.id == id);
        if (!j) { console.error('Job not found:', id); return; }
        
        document.getElementById('modal-title').textContent = j.title;
        document.getElementById('modal-company').innerHTML = `<i class="fas fa-building"></i> ${j.company}`;
        document.getElementById('modal-location').textContent = j.location;
        document.getElementById('modal-date').textContent = j.date;
        document.getElementById('modal-pay').textContent = `EGP ${j.pay}/day`;
        document.getElementById('modal-slots').textContent = `${j.slots_left} slots left`;
        document.getElementById('modal-description').textContent = j.description || 'No description provided by the client.';
        
        document.getElementById('modal-badge').innerHTML = j.status === 'active' 
            ? '<span class="status-badge status-active" style="display:inline-block">Hiring</span>' 
            : '<span class="status-badge status-pending" style="display:inline-block">Coming Soon</span>';
            
        const applyBtn = document.getElementById('modal-apply-btn');
        applyBtn.onclick = () => applyJob(applyBtn, j.id);
        applyBtn.textContent = 'Apply Now';
        applyBtn.style.background = 'var(--accent)';
        applyBtn.style.pointerEvents = 'auto';
        
        const modal = document.getElementById('project-modal');
        modal.style.display = 'flex';
    }

    function closeProjectModal() {
        document.getElementById('project-modal').style.display = 'none';
    }

    function applyJob(btn, projectId) {
        btn.textContent = 'Applying...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
        fetch('../api/apply_job.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ project_id: projectId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.textContent = '✓ Applied Successfully!';
                btn.style.background = 'var(--accent)';
                // Update local state
                const job = allJobs.find(j => j.id === projectId);
                if (job) job.already_applied = 'pending';
                renderJobs(allJobs);
                setTimeout(() => closeProjectModal(), 1500);
            } else {
                btn.textContent = data.error || 'Error';
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
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
