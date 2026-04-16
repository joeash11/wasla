<?php require_once __DIR__ . '/admin_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla Admin - Reports</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <style>
        .report-pending { border-left: 3px solid #ff9800; background: rgba(255,152,0,0.04) !important; }
        .report-reviewed { border-left: 3px solid #2979ff; }
        .report-resolved { border-left: 3px solid #00c853; }
        .report-dismissed { border-left: 3px solid #9e9e9e; opacity: 0.6; }
        .badge-pending { background: linear-gradient(135deg,#ff9800,#ffc107); color:#fff; }
        .badge-reviewed { background: linear-gradient(135deg,#2979ff,#448aff); color:#fff; }
        .badge-resolved { background: linear-gradient(135deg,#00c853,#00e676); color:#fff; }
        .badge-dismissed { background: #9e9e9e; color:#fff; }
        .report-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; }
        .reason-tag { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:6px; font-size:0.75rem; font-weight:600; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); }
        .reporter-role-client { color:#4fc3f7; }
        .reporter-role-usher { color:#ab47bc; }
        .report-desc-cell { max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:rgba(255,255,255,0.6); font-size:0.85rem; }
        .pending-notice { background:linear-gradient(135deg,rgba(255,152,0,0.1),rgba(255,193,7,0.1)); border:1px solid rgba(255,152,0,0.3); border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; align-items:center; gap:12px; color:#ff9800; font-size:0.92rem; font-weight:500; }
        .pending-notice .count { font-weight:800; font-size:1.1rem; color:#ff6d00; }
        
        /* Action buttons */
        .action-review { color:#2979ff !important; }
        .action-resolve { color:#00c853 !important; }
        .action-dismiss { color:#9e9e9e !important; }

        /* Modal */
        .report-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; z-index:9998; opacity:0; visibility:hidden; transition:all 0.3s ease; }
        .report-modal-overlay.show { opacity:1; visibility:visible; }
        .report-modal { background:var(--glass-bg,#1a1a2e); border:1px solid rgba(255,255,255,0.1); border-radius:16px; padding:0; max-width:520px; width:90%; transform:scale(0.85); transition:transform 0.3s cubic-bezier(0.22,1,0.36,1); }
        .report-modal-overlay.show .report-modal { transform:scale(1); }
        .report-modal-header { padding:24px 28px 16px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; }
        .report-modal-header h3 { font-size:1.1rem; font-weight:700; color:#fff; }
        .report-modal-close { background:none; border:1px solid rgba(255,255,255,0.15); color:#fff; width:32px; height:32px; border-radius:50%; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; transition:all 0.2s ease; }
        .report-modal-close:hover { border-color:#ff5252; color:#ff5252; }
        .report-modal-body { padding:20px 28px 28px; }
        .report-detail { margin-bottom:14px; font-size:0.9rem; }
        .report-detail strong { color:rgba(255,255,255,0.9); }
        .report-detail span { color:rgba(255,255,255,0.6); }
        .report-description-full { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:14px; color:rgba(255,255,255,0.7); font-size:0.88rem; line-height:1.6; margin:12px 0; }
        .report-admin-notes { margin-top:16px; }
        .report-admin-notes textarea { width:100%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:12px; color:#fff; font-size:0.88rem; resize:vertical; min-height:80px; font-family:inherit; }
        .report-admin-notes textarea::placeholder { color:rgba(255,255,255,0.3); }
        .report-modal-actions { display:flex; gap:10px; margin-top:20px; flex-wrap:wrap; }
        .report-action-btn { padding:10px 20px; border-radius:10px; border:none; cursor:pointer; font-weight:600; font-size:0.85rem; display:inline-flex; align-items:center; gap:6px; transition:all 0.2s ease; }
        .report-action-btn:hover { transform:translateY(-1px); }
        .btn-mark-reviewed { background:linear-gradient(135deg,#2979ff,#448aff); color:#fff; }
        .btn-mark-resolved { background:linear-gradient(135deg,#00c853,#00e676); color:#fff; }
        .btn-mark-dismissed { background:rgba(158,158,158,0.3); color:#fff; border:1px solid rgba(158,158,158,0.4) !important; }
        .btn-cancel-modal { background:transparent; color:rgba(255,255,255,0.6); border:1px solid rgba(255,255,255,0.15) !important; }

        /* Toast */
        .toast { position:fixed; bottom:30px; right:30px; padding:14px 24px; border-radius:12px; color:#fff; font-weight:600; font-size:0.9rem; display:flex; align-items:center; gap:10px; z-index:9999; transform:translateY(100px); opacity:0; transition:all 0.4s cubic-bezier(0.22,1,0.36,1); box-shadow:0 8px 30px rgba(0,0,0,0.3); }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast-success { background:linear-gradient(135deg,#00c853,#00e676); }
        .toast-error { background:linear-gradient(135deg,#ff1744,#ff5252); }
    </style>
</head>
<body>
    <nav class="navbar admin-navbar"><div class="navbar-left">
        <a href="dashboard.php" class="logo"><img src="../images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36"><span class="logo-text">Wasla</span> <span class="admin-tag">Admin</span></a>
        <ul class="nav-links"><li><a href="dashboard.php">Dashboard</a></li><li><a href="users.php">Users</a></li><li><a href="projects.php">Projects</a></li><li><a href="reports.php" class="active">Reports</a></li></ul>
    </div><div class="navbar-right"><span class="welcome-text">Admin Panel</span><div class="user-avatar-small"><i class="fas fa-user-shield"></i></div></div></nav>
    <div class="main-wrapper">
        <aside class="sidebar admin-sidebar">
            <div class="sidebar-profile"><div class="profile-avatar"><i class="fas fa-user-shield"></i></div><h3 class="profile-name">System<br>Admin</h3></div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link"><i class="fas fa-th-large"></i><span>Dashboard</span></a>
                <a href="users.php" class="sidebar-link"><i class="fas fa-users"></i><span>Users</span></a>
                <a href="projects.php" class="sidebar-link"><i class="fas fa-folder-open"></i><span>Projects</span></a>
                <a href="reports.php" class="sidebar-link active"><i class="fas fa-flag"></i><span>Reports</span></a>
            </nav>
            <div class="sidebar-footer"><button class="btn-logout" onclick="window.location.href='../auth_logout.php'">Log Out</button></div>
        </aside>
        <main class="content">
            <div class="page-header"><h1 class="section-title">Reports Management</h1></div>
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Total Reports</p><h2 class="stat-value" id="stat-total">—</h2></div>
                <div class="stat-card"><p class="stat-label">Pending</p><h2 class="stat-value" id="stat-pending" style="color:#ff9800">—</h2></div>
                <div class="stat-card"><p class="stat-label">Resolved</p><h2 class="stat-value" id="stat-resolved" style="color:#00c853">—</h2></div>
                <div class="stat-card"><p class="stat-label">Dismissed</p><h2 class="stat-value" id="stat-dismissed">—</h2></div>
            </section>
            <div class="pending-notice" id="pending-notice" style="display:none">
                <i class="fas fa-exclamation-triangle"></i>
                <span><span class="count" id="pending-count">0</span> report(s) are awaiting your review.</span>
            </div>
            <section class="filters-row">
                <div class="filter-search"><i class="fas fa-search"></i><input type="text" placeholder="Search reports..." id="report-search"></div>
                <div class="filter-select"><select id="status-filter"><option value="">All Status</option><option value="pending">Pending</option><option value="reviewed">Reviewed</option><option value="resolved">Resolved</option><option value="dismissed">Dismissed</option></select></div>
                <div class="filter-select"><select id="role-filter"><option value="">All Reporters</option><option value="client">From Clients</option><option value="usher">From Ushers</option></select></div>
            </section>
            <div class="admin-table-wrap">
                <table class="admin-table" id="reports-table">
                    <thead><tr><th>Reporter</th><th>Reported</th><th>Project</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody id="reports-tbody">
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:rgba(255,255,255,0.4)"><i class="fas fa-spinner fa-spin"></i> Loading reports...</td></tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Report Detail Modal -->
    <div class="report-modal-overlay" id="report-modal">
        <div class="report-modal">
            <div class="report-modal-header">
                <h3 id="modal-title">Report Details</h3>
                <button class="report-modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="report-modal-body" id="modal-body"></div>
        </div>
    </div>
    <div class="toast" id="toast"><i class="fas fa-check-circle"></i> <span id="toast-text"></span></div>

    <footer class="footer"><div class="footer-left"><h3>Wasla</h3><p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p></div><div class="footer-links"><a href="../terms.php">TERMS OF SERVICE</a><a href="../privacy.php">PRIVACY POLICY</a><a href="../contact.php">CONTACT US</a></div></footer>

    <script>
    let allReports = [];
    const reasonLabels = {
        unprofessional: 'Unprofessional',
        no_show: 'No Show',
        harassment: 'Harassment',
        late: 'Late / Tardy',
        payment_issue: 'Payment Issue',
        safety: 'Safety Concern',
        other: 'Other'
    };

    async function loadReports() {
        try {
            const res = await fetch('../api/reports.php?action=all_reports');
            const data = await res.json();
            if (data.success) {
                allReports = data.reports;
                updateStats(data.stats);
                renderTable(allReports);
            } else {
                renderFallback();
            }
        } catch {
            renderFallback();
        }
    }

    function renderFallback() {
        allReports = [
            { id:1, reporter_name:'Abdullah Elsayed', reporter_email:'abdullah@company.com', reporter_role:'client', reported_name:'Ahmed Mohamed', reported_email:'ahmed@example.com', project_title:'Fashion Week Riyadh', reason:'late', description:'The usher arrived 45 minutes late to the event and missed the VIP check-in period.', status:'pending', created_at:'2024-06-16 10:00:00', admin_notes:'' },
            { id:2, reporter_name:'Ahmed Mohamed', reporter_email:'ahmed@example.com', reporter_role:'usher', reported_name:'Abdullah Elsayed', reported_email:'abdullah@company.com', project_title:'Food Festival', reason:'payment_issue', description:'I completed the event but payment has not been released after 2 weeks.', status:'pending', created_at:'2024-06-20 14:00:00', admin_notes:'' },
            { id:3, reporter_name:'Sara Ahmed', reporter_email:'sara@mdlbeast.com', reporter_role:'client', reported_name:'Fatimah Al-Saud', reported_email:'fatimah@email.com', project_title:'MDLBEAST Soundstorm', reason:'unprofessional', description:'Was on phone during guests handling and did not follow dress code.', status:'reviewed', created_at:'2024-06-10 09:00:00', admin_notes:'Contacted both parties' },
        ];
        const stats = { total:3, pending:2, reviewed:1, resolved:0, dismissed:0 };
        updateStats(stats);
        renderTable(allReports);
    }

    function updateStats(stats) {
        document.getElementById('stat-total').textContent = stats.total;
        document.getElementById('stat-pending').textContent = stats.pending;
        document.getElementById('stat-resolved').textContent = stats.resolved;
        document.getElementById('stat-dismissed').textContent = stats.dismissed;
        const notice = document.getElementById('pending-notice');
        if (stats.pending > 0) { notice.style.display='flex'; document.getElementById('pending-count').textContent=stats.pending; }
        else { notice.style.display='none'; }
    }

    function renderTable(reports) {
        const tbody = document.getElementById('reports-tbody');
        tbody.innerHTML = '';
        if (reports.length === 0) { tbody.innerHTML='<tr><td colspan="7" style="text-align:center;padding:40px;color:rgba(255,255,255,0.4)">No reports found</td></tr>'; return; }
        reports.forEach(r => {
            const tr = document.createElement('tr');
            tr.className = `report-${r.status}`;
            tr.dataset.status = r.status;
            tr.dataset.role = r.reporter_role;
            const badge = `<span class="report-badge badge-${r.status}">${r.status}</span>`;
            const roleClass = r.reporter_role === 'client' ? 'reporter-role-client' : 'reporter-role-usher';
            const reason = `<span class="reason-tag">${reasonLabels[r.reason]||r.reason}</span>`;
            const date = new Date(r.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
            let actions = `<button class="table-action-btn" title="View" onclick="viewReport(${r.id})"><i class="fas fa-eye"></i></button>`;
            if (r.status === 'pending') {
                actions += `<button class="table-action-btn action-review" title="Mark Reviewed" onclick="updateReport(${r.id},'reviewed')"><i class="fas fa-clipboard-check"></i></button>`;
                actions += `<button class="table-action-btn action-resolve" title="Resolve" onclick="updateReport(${r.id},'resolved')"><i class="fas fa-check-circle"></i></button>`;
                actions += `<button class="table-action-btn action-dismiss" title="Dismiss" onclick="updateReport(${r.id},'dismissed')"><i class="fas fa-times-circle"></i></button>`;
            } else if (r.status === 'reviewed') {
                actions += `<button class="table-action-btn action-resolve" title="Resolve" onclick="updateReport(${r.id},'resolved')"><i class="fas fa-check-circle"></i></button>`;
                actions += `<button class="table-action-btn action-dismiss" title="Dismiss" onclick="updateReport(${r.id},'dismissed')"><i class="fas fa-times-circle"></i></button>`;
            }
            tr.innerHTML = `
                <td><strong class="${roleClass}">${esc(r.reporter_name)}</strong><br><small style="color:rgba(255,255,255,0.4)">${r.reporter_role}</small></td>
                <td><strong>${esc(r.reported_name)}</strong></td>
                <td>${esc(r.project_title)}</td>
                <td>${reason}</td>
                <td>${badge}</td>
                <td><small>${date}</small></td>
                <td class="action-cell">${actions}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function viewReport(id) {
        const r = allReports.find(x => x.id == id);
        if (!r) return;
        document.getElementById('modal-title').textContent = `Report #${r.id}`;
        document.getElementById('modal-body').innerHTML = `
            <div class="report-detail"><strong>Reporter:</strong> <span>${esc(r.reporter_name)} (${r.reporter_role})</span></div>
            <div class="report-detail"><strong>Reported:</strong> <span>${esc(r.reported_name)}</span></div>
            <div class="report-detail"><strong>Project:</strong> <span>${esc(r.project_title)}</span></div>
            <div class="report-detail"><strong>Reason:</strong> <span>${reasonLabels[r.reason]||r.reason}</span></div>
            <div class="report-detail"><strong>Status:</strong> <span class="report-badge badge-${r.status}">${r.status}</span></div>
            <div class="report-detail"><strong>Filed:</strong> <span>${new Date(r.created_at).toLocaleString()}</span></div>
            <div class="report-description-full">${esc(r.description)}</div>
            ${r.admin_notes ? `<div class="report-detail"><strong>Admin Notes:</strong><div class="report-description-full">${esc(r.admin_notes)}</div></div>` : ''}
            <div class="report-admin-notes">
                <label style="font-size:0.85rem;font-weight:600;color:rgba(255,255,255,0.7)">Admin Notes:</label>
                <textarea id="admin-notes-input" placeholder="Add notes about this report...">${r.admin_notes||''}</textarea>
            </div>
            <div class="report-modal-actions">
                ${r.status!=='resolved'?`<button class="report-action-btn btn-mark-resolved" onclick="updateReportWithNotes(${r.id},'resolved')"><i class="fas fa-check-circle"></i> Resolve</button>`:''}
                ${r.status==='pending'?`<button class="report-action-btn btn-mark-reviewed" onclick="updateReportWithNotes(${r.id},'reviewed')"><i class="fas fa-clipboard-check"></i> Mark Reviewed</button>`:''}
                ${r.status!=='dismissed'?`<button class="report-action-btn btn-mark-dismissed" onclick="updateReportWithNotes(${r.id},'dismissed')"><i class="fas fa-times-circle"></i> Dismiss</button>`:''}
                <button class="report-action-btn btn-cancel-modal" onclick="closeModal()">Close</button>
            </div>
        `;
        document.getElementById('report-modal').classList.add('show');
    }

    function closeModal() { document.getElementById('report-modal').classList.remove('show'); }
    document.getElementById('report-modal').addEventListener('click', e => { if (e.target===e.currentTarget) closeModal(); });

    async function updateReport(id, action) {
        try {
            const res = await fetch('../api/reports.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ report_id:id, action }) });
            const data = await res.json();
            if (data.success) { showToast(data.message,'success'); await loadReports(); }
            else showToast(data.error||'Failed','error');
        } catch {
            // Fallback
            const r = allReports.find(x=>x.id==id);
            if(r) r.status=action;
            const stats = { total:allReports.length, pending:0, reviewed:0, resolved:0, dismissed:0 };
            allReports.forEach(x => { if(stats[x.status]!==undefined) stats[x.status]++; });
            updateStats(stats); renderTable(allReports);
            showToast(`Report marked as ${action}`,'success');
        }
    }

    async function updateReportWithNotes(id, action) {
        const notes = document.getElementById('admin-notes-input')?.value || '';
        try {
            const res = await fetch('../api/reports.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ report_id:id, action, admin_notes:notes }) });
            const data = await res.json();
            if (data.success) { closeModal(); showToast(data.message,'success'); await loadReports(); }
            else showToast(data.error||'Failed','error');
        } catch {
            const r = allReports.find(x=>x.id==id);
            if(r) { r.status=action; r.admin_notes=notes; }
            closeModal();
            const stats = { total:allReports.length, pending:0, reviewed:0, resolved:0, dismissed:0 };
            allReports.forEach(x => { if(stats[x.status]!==undefined) stats[x.status]++; });
            updateStats(stats); renderTable(allReports);
            showToast(`Report marked as ${action}`,'success');
        }
    }

    // Search & filter
    document.getElementById('report-search').addEventListener('input', e => {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('#reports-tbody tr').forEach(r => { r.style.display = r.textContent.toLowerCase().includes(q)?'':'none'; });
    });
    document.getElementById('status-filter').addEventListener('change', e => { applyFilters(); });
    document.getElementById('role-filter').addEventListener('change', e => { applyFilters(); });
    function applyFilters() {
        const s = document.getElementById('status-filter').value;
        const r = document.getElementById('role-filter').value;
        const filtered = allReports.filter(x => (!s || x.status===s) && (!r || x.reporter_role===r));
        renderTable(filtered);
    }

    function showToast(msg, type='success') {
        const t = document.getElementById('toast');
        t.className = `toast toast-${type}`;
        document.getElementById('toast-text').textContent = msg;
        t.querySelector('i').className = type==='success'?'fas fa-check-circle':'fas fa-exclamation-circle';
        requestAnimationFrame(()=>t.classList.add('show'));
        setTimeout(()=>t.classList.remove('show'),3500);
    }

    function esc(t) { if(!t)return''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }

    document.addEventListener('DOMContentLoaded', loadReports);
    </script>
</body>
</html>
