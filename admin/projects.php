<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla Admin - Projects</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <style>
        /* Admin Project Review Styles */
        .approve-btn {
            background: linear-gradient(135deg, #00c853, #00e676);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0,200,83,0.25);
        }
        .approve-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0,200,83,0.4);
        }
        .decline-btn {
            background: linear-gradient(135deg, #ff1744, #ff5252);
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(255,23,68,0.25);
        }
        .decline-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(255,23,68,0.4);
        }
        .action-cell {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        .status-pending-review {
            background: linear-gradient(135deg, #ff9800, #ffc107) !important;
            color: #fff !important;
            animation: pulse-pending 2s ease-in-out infinite;
        }
        @keyframes pulse-pending {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .status-cancelled-badge {
            background: #e74c3c !important;
            color: #fff !important;
        }
        .pending-notice {
            background: linear-gradient(135deg, rgba(255,152,0,0.1), rgba(255,193,7,0.1));
            border: 1px solid rgba(255,152,0,0.3);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ff9800;
            font-size: 0.92rem;
            font-weight: 500;
        }
        .pending-notice i {
            font-size: 1.3rem;
        }
        .pending-notice .count {
            font-weight: 800;
            font-size: 1.1rem;
            color: #ff6d00;
        }
        .table-row-pending {
            background: rgba(255,152,0,0.04) !important;
            border-left: 3px solid #ff9800;
        }
        .table-row-cancelled {
            opacity: 0.6;
        }
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 14px 24px;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 9999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-success { background: linear-gradient(135deg, #00c853, #00e676); }
        .toast-error { background: linear-gradient(135deg, #ff1744, #ff5252); }
        .toast-info { background: linear-gradient(135deg, #2979ff, #448aff); }

        /* Confirmation modal */
        .confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .confirm-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .confirm-dialog {
            background: var(--glass-bg, #1a1a2e);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 32px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            transform: scale(0.85);
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .confirm-overlay.show .confirm-dialog {
            transform: scale(1);
        }
        .confirm-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.5rem;
        }
        .confirm-icon-approve {
            background: rgba(0,200,83,0.15);
            color: #00c853;
        }
        .confirm-icon-decline {
            background: rgba(255,23,68,0.15);
            color: #ff1744;
        }
        .confirm-icon-flag {
            background: rgba(255,152,0,0.15);
            color: #ff9800;
        }
        .confirm-icon-remove {
            background: rgba(255,23,68,0.15);
            color: #ff1744;
        }
        .confirm-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }
        .confirm-text {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .confirm-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .confirm-cancel {
            padding: 10px 24px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.15);
            background: transparent;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .confirm-cancel:hover {
            background: rgba(255,255,255,0.08);
        }
        .confirm-ok {
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .confirm-ok-approve { background: linear-gradient(135deg, #00c853, #00e676); }
        .confirm-ok-decline { background: linear-gradient(135deg, #ff1744, #ff5252); }
        .confirm-ok-flag { background: linear-gradient(135deg, #ff9800, #ffc107); }
        .confirm-ok-remove { background: linear-gradient(135deg, #ff1744, #ff5252); }
        .confirm-ok:hover { transform: translateY(-1px); }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px;
            color: rgba(255,255,255,0.5);
            font-size: 1rem;
            gap: 10px;
        }
        .loading-spinner i {
            font-size: 1.4rem;
        }
    </style>
</head>
<body>
    <nav class="navbar admin-navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span> <span class="admin-tag">Admin</span></a>
        <ul class="nav-links"><li><a href="dashboard.php">Dashboard</a></li><li><a href="users.php">Users</a></li><li><a href="projects.php" class="active">Projects</a></li><li><a href="reports.php">Reports</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Admin Panel</span><div class="user-avatar-small"><i class="fas fa-user-shield"></i></div></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar admin-sidebar">
            <div class="sidebar-profile"><div class="profile-avatar"><i class="fas fa-user-shield"></i></div><h3 class="profile-name">System<br>Admin</h3></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="users.php" class="sidebar-link"><i class="fas fa-users"></i><span>Users</span></a>
                <a href="projects.php" class="sidebar-link active"><i class="fas fa-folder-open"></i><span>Projects</span></a>
                <a href="reports.php" class="sidebar-link"><i class="fas fa-flag"></i><span>Reports</span></a>
            </nav>
            <div class="sidebar-footer"><button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <div class="page-header"><h1 class="section-title">Project Oversight</h1></div>
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Total Projects</p><h2 class="stat-value" id="stat-total">—</h2></div>
                <div class="stat-card"><p class="stat-label">Pending Review</p><h2 class="stat-value" id="stat-pending" style="color:#ff9800">—</h2></div>
                <div class="stat-card"><p class="stat-label">Active</p><h2 class="stat-value" id="stat-active">—</h2></div>
                <div class="stat-card"><p class="stat-label">Completed</p><h2 class="stat-value" id="stat-completed">—</h2></div>
            </section>

            <!-- Pending Notice -->
            <div class="pending-notice" id="pending-notice" style="display:none">
                <i class="fas fa-exclamation-triangle"></i>
                <span><span class="count" id="pending-count">0</span> project(s) are awaiting your review. Please approve or decline them below.</span>
            </div>

            <section class="filters-row">
                <div class="filter-search"><i class="fas fa-search"></i><input type="text" placeholder="Search projects..." id="project-search"></div>
                <div class="filter-select"><select id="proj-status-filter"><option value="">All Status</option><option value="pending">Pending</option><option value="active">Active</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
            </section>
            <div class="admin-table-wrap">
                <table class="admin-table" id="projects-table">
                    <thead><tr><th>Project</th><th>Client</th><th>Location</th><th>Date</th><th>Ushers</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody id="projects-tbody">
                        <tr><td colspan="7"><div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading projects...</div></td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- View Modal -->
    <div class="admin-modal-overlay" id="project-modal" style="display:none">
        <div class="admin-modal">
            <div class="admin-modal-header">
                <h3 id="modal-title">Project Details</h3>
                <button class="admin-modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="admin-modal-body" id="modal-body"></div>
        </div>
    </div>

    <!-- Confirmation Dialog -->
    <div class="confirm-overlay" id="confirm-overlay">
        <div class="confirm-dialog">
            <div class="confirm-icon" id="confirm-icon"><i class="fas fa-check"></i></div>
            <div class="confirm-title" id="confirm-title">Confirm Action</div>
            <div class="confirm-text" id="confirm-text">Are you sure?</div>
            <div class="confirm-actions">
                <button class="confirm-cancel" onclick="closeConfirm()">Cancel</button>
                <button class="confirm-ok" id="confirm-ok" onclick="executeConfirm()">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"><i class="fas fa-check-circle"></i> <span id="toast-text"></span></div>

    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>

    <script>
    let allProjects = [];
    let pendingAction = null; // { projectId, action, row }

    // ========== Load projects from API ==========
    async function loadProjects() {
        try {
            const res = await fetch('../api/admin_projects.php');
            if (!res.ok) {
                // API returned error (e.g. 403 not logged in) - use fallback silently
                renderFallbackTable();
                return;
            }
            const data = await res.json();
            
            if (data.success && data.projects && data.projects.length > 0) {
                allProjects = data.projects;
                updateStats(data.stats);
                renderTable(allProjects);
            } else {
                // API succeeded but no projects found - use fallback
                renderFallbackTable();
            }
        } catch (err) {
            // API not available - use fallback silently
            renderFallbackTable();
        }
    }

    // ========== Fallback: static data (works without backend) ==========
    function renderFallbackTable() {
        const fallback = [
            { id: 1, title: 'Gaming Festival 2024', client_name: 'Abdullah Elsayed', company_name: 'Gulf Events Co.', city: 'Cairo', location: 'Riyadh Exhibition Center', event_date: '2024-06-28', ushers_needed: 20, accepted_ushers: 15, status: 'active' },
            { id: 2, title: 'MDLBEAST Soundstorm', client_name: 'Sara Ahmed', company_name: 'MDLBEAST', city: 'Riyadh', location: 'Banban, Riyadh', event_date: '2024-07-02', ushers_needed: 40, accepted_ushers: 28, status: 'active' },
            { id: 3, title: 'Fashion Week Riyadh', client_name: 'Abdullah Elsayed', company_name: 'Gulf Events Co.', city: 'Cairo', location: 'The Ritz-Carlton', event_date: '2024-06-10', ushers_needed: 12, accepted_ushers: 12, status: 'completed' },
            { id: 4, title: 'Annual Charity Gala', client_name: 'Abdullah Elsayed', company_name: 'Gulf Events Co.', city: 'Jeddah', location: 'Al Faisaliyah Hotel', event_date: '2024-06-05', ushers_needed: 8, accepted_ushers: 8, status: 'completed' },
            { id: 5, title: 'Corporate Summit KSA', client_name: 'Sara Ahmed', company_name: 'MDLBEAST', city: 'Jeddah', location: 'Hilton Jeddah', event_date: '2024-07-10', ushers_needed: 8, accepted_ushers: 5, status: 'active' },
            { id: 6, title: 'Food Festival', client_name: 'Abdullah Elsayed', company_name: 'Gulf Events Co.', city: 'Jeddah', location: 'Jeddah Corniche', event_date: '2024-06-01', ushers_needed: 10, accepted_ushers: 0, status: 'cancelled' },
            { id: 7, title: 'Tech Innovation Expo', client_name: 'Mohammed Ali', company_name: 'Dubai Tech', city: 'Dubai', location: 'DIFC', event_date: '2024-08-05', ushers_needed: 20, accepted_ushers: 0, status: 'pending' },
            { id: 8, title: 'Riyadh Season Launch', client_name: 'Nora Khalid', company_name: 'Riyadh Season', city: 'Riyadh', location: 'Boulevard Riyadh', event_date: '2024-09-01', ushers_needed: 50, accepted_ushers: 0, status: 'pending' },
        ];
        allProjects = fallback;
        const stats = { total: fallback.length, pending: 0, active: 0, completed: 0, cancelled: 0 };
        fallback.forEach(p => { if (stats[p.status] !== undefined) stats[p.status]++; });
        updateStats(stats);
        renderTable(fallback);
    }

    // ========== Update stat cards ==========
    function updateStats(stats) {
        document.getElementById('stat-total').textContent = stats.total;
        document.getElementById('stat-pending').textContent = stats.pending;
        document.getElementById('stat-active').textContent = stats.active;
        document.getElementById('stat-completed').textContent = stats.completed;

        const notice = document.getElementById('pending-notice');
        if (stats.pending > 0) {
            notice.style.display = 'flex';
            document.getElementById('pending-count').textContent = stats.pending;
        } else {
            notice.style.display = 'none';
        }
    }

    // ========== Render table ==========
    function renderTable(projects) {
        const tbody = document.getElementById('projects-tbody');
        tbody.innerHTML = '';

        if (projects.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:40px;color:rgba(255,255,255,0.4)">No projects found</td></tr>';
            return;
        }

        projects.forEach(p => {
            const tr = document.createElement('tr');
            tr.dataset.status = p.status;
            tr.dataset.id = p.id;

            if (p.status === 'pending') tr.classList.add('table-row-pending');
            if (p.status === 'cancelled') tr.classList.add('table-row-cancelled');

            const dateFormatted = formatDate(p.event_date);
            const clientDisplay = p.company_name || p.client_name;
            const ushersDisplay = `${p.accepted_ushers || 0}/${p.ushers_needed}`;

            let statusBadge = '';
            switch (p.status) {
                case 'pending':
                    statusBadge = '<span class="card-badge status-pending-review" style="position:static"><i class="fas fa-clock"></i> Pending Review</span>';
                    break;
                case 'active':
                    statusBadge = '<span class="card-badge status-active" style="position:static">Active</span>';
                    break;
                case 'completed':
                    statusBadge = '<span class="card-badge status-completed" style="position:static">Completed</span>';
                    break;
                case 'cancelled':
                    statusBadge = '<span class="card-badge status-cancelled-badge" style="position:static">Declined</span>';
                    break;
            }

            let actions = `<button class="table-action-btn" title="View" onclick="viewProject(${p.id})"><i class="fas fa-eye"></i></button>`;

            if (p.status === 'pending') {
                actions += `
                    <button class="approve-btn" title="Approve" onclick="confirmAction(${p.id}, 'approve')"><i class="fas fa-check"></i> Approve</button>
                    <button class="decline-btn" title="Decline" onclick="confirmAction(${p.id}, 'decline')"><i class="fas fa-times"></i> Decline</button>
                `;
            } else if (p.status === 'active') {
                actions += `<button class="table-action-btn table-action-warn" title="Flag" onclick="confirmAction(${p.id}, 'flag')"><i class="fas fa-flag"></i></button>`;
            } else if (p.status === 'cancelled') {
                actions += `<button class="table-action-btn" title="Reactivate" style="color:var(--accent)" onclick="confirmAction(${p.id}, 'reactivate')"><i class="fas fa-redo"></i></button>`;
                actions += `<button class="table-action-btn table-action-danger" title="Remove" onclick="confirmAction(${p.id}, 'remove')"><i class="fas fa-trash"></i></button>`;
            }

            tr.innerHTML = `
                <td><strong>${escapeHtml(p.title)}</strong></td>
                <td>${escapeHtml(clientDisplay)}</td>
                <td>${escapeHtml(p.city || p.location)}</td>
                <td>${dateFormatted}</td>
                <td>${ushersDisplay}</td>
                <td>${statusBadge}</td>
                <td class="action-cell">${actions}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    // ========== Confirm Dialog ==========
    function confirmAction(projectId, action) {
        const project = allProjects.find(p => p.id == projectId);
        if (!project) return;

        const overlay = document.getElementById('confirm-overlay');
        const icon = document.getElementById('confirm-icon');
        const title = document.getElementById('confirm-title');
        const text = document.getElementById('confirm-text');
        const okBtn = document.getElementById('confirm-ok');

        // Reset classes
        okBtn.className = 'confirm-ok';

        switch (action) {
            case 'approve':
                icon.className = 'confirm-icon confirm-icon-approve';
                icon.innerHTML = '<i class="fas fa-check-circle"></i>';
                title.textContent = 'Approve Project';
                text.innerHTML = `Approve <strong>"${escapeHtml(project.title)}"</strong>?<br>This will make the project visible to ushers and allow applications.`;
                okBtn.classList.add('confirm-ok-approve');
                okBtn.textContent = 'Approve';
                break;
            case 'decline':
                icon.className = 'confirm-icon confirm-icon-decline';
                icon.innerHTML = '<i class="fas fa-times-circle"></i>';
                title.textContent = 'Decline Project';
                text.innerHTML = `Decline <strong>"${escapeHtml(project.title)}"</strong>?<br>The client will be notified that their project was not approved.`;
                okBtn.classList.add('confirm-ok-decline');
                okBtn.textContent = 'Decline';
                break;
            case 'flag':
                icon.className = 'confirm-icon confirm-icon-flag';
                icon.innerHTML = '<i class="fas fa-flag"></i>';
                title.textContent = 'Flag Project';
                text.innerHTML = `Flag <strong>"${escapeHtml(project.title)}"</strong>?<br>This will suspend the project and mark it for review.`;
                okBtn.classList.add('confirm-ok-flag');
                okBtn.textContent = 'Flag It';
                break;
            case 'reactivate':
                icon.className = 'confirm-icon confirm-icon-approve';
                icon.innerHTML = '<i class="fas fa-redo"></i>';
                title.textContent = 'Reactivate Project';
                text.innerHTML = `Reactivate <strong>"${escapeHtml(project.title)}"</strong>?<br>This will set the project back to active.`;
                okBtn.classList.add('confirm-ok-approve');
                okBtn.textContent = 'Reactivate';
                break;
            case 'remove':
                icon.className = 'confirm-icon confirm-icon-remove';
                icon.innerHTML = '<i class="fas fa-trash-alt"></i>';
                title.textContent = 'Remove Project';
                text.innerHTML = `Permanently remove <strong>"${escapeHtml(project.title)}"</strong>?<br><span style="color:#ff5252">This action cannot be undone.</span>`;
                okBtn.classList.add('confirm-ok-remove');
                okBtn.textContent = 'Remove';
                break;
        }

        pendingAction = { projectId, action };
        overlay.classList.add('show');
    }

    function closeConfirm() {
        document.getElementById('confirm-overlay').classList.remove('show');
        pendingAction = null;
    }

    async function executeConfirm() {
        if (!pendingAction) return;

        const { projectId, action } = pendingAction;
        const okBtn = document.getElementById('confirm-ok');
        okBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        okBtn.disabled = true;

        try {
            const res = await fetch('../api/admin_projects.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ project_id: projectId, action: action })
            });
            const data = await res.json();

            if (data.success) {
                closeConfirm();
                showToast(data.message, 'success');
                // Reload projects
                await loadProjects();
            } else {
                showToast(data.error || 'Action failed', 'error');
            }
        } catch (err) {
            // Fallback: update locally if API unavailable
            const project = allProjects.find(p => p.id == projectId);
            if (project) {
                if (action === 'approve') project.status = 'active';
                else if (action === 'decline') project.status = 'cancelled';
                else if (action === 'flag') project.status = 'cancelled';
                else if (action === 'reactivate') project.status = 'active';
                else if (action === 'remove') {
                    allProjects = allProjects.filter(p => p.id != projectId);
                }
                
                const stats = { total: allProjects.length, pending: 0, active: 0, completed: 0, cancelled: 0 };
                allProjects.forEach(p => { if (stats[p.status] !== undefined) stats[p.status]++; });
                updateStats(stats);
                renderTable(allProjects);
                
                const msgs = {
                    approve: `Project approved successfully!`,
                    decline: `Project declined.`,
                    flag: `Project flagged for review.`,
                    reactivate: `Project reactivated.`,
                    remove: `Project removed permanently.`
                };
                showToast(msgs[action], 'success');
            }
            closeConfirm();
        }

        okBtn.disabled = false;
    }

    // ========== View Project Modal ==========
    function viewProject(projectId) {
        const p = allProjects.find(proj => proj.id == projectId);
        if (!p) return;

        const clientDisplay = p.company_name ? `${p.company_name} (${p.client_name})` : p.client_name;
        const statusLabels = { pending: 'Pending Review', active: 'Active', completed: 'Completed', cancelled: 'Declined / Cancelled' };

        document.getElementById('modal-title').textContent = p.title;
        document.getElementById('modal-body').innerHTML = `
            <div class="modal-detail"><strong>Client:</strong> ${escapeHtml(clientDisplay)}</div>
            <div class="modal-detail"><strong>Location:</strong> ${escapeHtml(p.location || p.city)}</div>
            <div class="modal-detail"><strong>City:</strong> ${escapeHtml(p.city)}</div>
            <div class="modal-detail"><strong>Event Date:</strong> ${formatDate(p.event_date)}${p.end_date ? ' — ' + formatDate(p.end_date) : ''}</div>
            <div class="modal-detail"><strong>Ushers:</strong> ${p.accepted_ushers || 0} / ${p.ushers_needed}</div>
            <div class="modal-detail"><strong>Pay per Usher:</strong> ${p.pay_per_usher ? 'EGP ' + parseFloat(p.pay_per_usher).toLocaleString() : 'N/A'}</div>
            <div class="modal-detail"><strong>Category:</strong> ${escapeHtml(p.category || 'General')}</div>
            <div class="modal-detail"><strong>Status:</strong> ${statusLabels[p.status] || p.status}</div>
            ${p.description ? `<div class="modal-detail" style="margin-top:12px"><strong>Description:</strong><br><span style="color:rgba(255,255,255,0.6)">${escapeHtml(p.description)}</span></div>` : ''}
        `;
        document.getElementById('project-modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('project-modal').style.display = 'none';
    }
    document.getElementById('project-modal').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeModal();
    });

    // ========== Search & Filter ==========
    document.getElementById('project-search').addEventListener('input', e => {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('#projects-tbody tr').forEach(r => {
            r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
    document.getElementById('proj-status-filter').addEventListener('change', e => {
        const v = e.target.value;
        const filtered = v ? allProjects.filter(p => p.status === v) : allProjects;
        renderTable(filtered);
    });

    // ========== Toast ==========
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toast-text');
        const icon = toast.querySelector('i');
        
        toast.className = `toast toast-${type}`;
        toastText.textContent = message;
        
        if (type === 'success') icon.className = 'fas fa-check-circle';
        else if (type === 'error') icon.className = 'fas fa-exclamation-circle';
        else icon.className = 'fas fa-info-circle';

        requestAnimationFrame(() => {
            toast.classList.add('show');
        });
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    // ========== Helpers ==========
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ========== Init ==========
    document.addEventListener('DOMContentLoaded', loadProjects);
    </script>
</body>
</html>
