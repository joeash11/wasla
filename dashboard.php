<?php require_once __DIR__ . '/includes/client_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Dashboard</title>
    <meta name="description"
        content="Wasla project management dashboard - manage your events, ushers and projects all in one place.">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
</head>

<body>
    <!-- Top Navbar -->
    <nav class="navbar" id="navbar">
        <div class="navbar-left">
            <a href="dashboard.php" class="logo">
                <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36">
                <span class="logo-text">Wasla</span>
            </a>
            <ul class="nav-links">
                <li><a href="dashboard.php" class="active" id="nav-dashboard">Dashboard</a></li>
                <li><a href="projects.php" id="nav-projects">My Projects</a></li>
                <li><a href="profile.php" id="nav-profile">Profile</a></li>
            </ul>
        </div>
        <div class="navbar-right">
            <span class="welcome-text">Welcome <?php echo htmlspecialchars($first_name); ?></span>
            <a href="profile.php" class="user-avatar-small">
                <i class="fas fa-user-circle"></i>
            </a>
            <a href="create-project.php" class="btn-create" id="btn-create-project">Create Project</a>
        </div>
    </nav>

    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-profile">
                <a href="profile.php" class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </a>
                <?php
                    $name_parts = explode(' ', $user_name);
                    $sidebar_first = $name_parts[0] ?? '';
                    $sidebar_last = isset($name_parts[1]) ? $name_parts[1] : '';
                ?>
                <h3 class="profile-name"><?php echo htmlspecialchars($sidebar_first); ?><br><?php echo htmlspecialchars($sidebar_last); ?></h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link active" id="side-dashboard">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <a href="projects.php" class="sidebar-link" id="side-projects">
                    <i class="fas fa-file-alt"></i>
                    <span>My Projects</span>
                </a>
                <a href="messages.php" class="sidebar-link" id="side-messages">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
                <a href="settings.php" class="sidebar-link" id="side-settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="help.php" class="sidebar-link" id="help-center">
                    <i class="fas fa-question-circle"></i>
                    <span>Help Center</span>
                </a>
                <a href="contact.php" class="sidebar-link">
                    <i class="fas fa-envelope"></i>
                    <span>Contact Us</span>
                </a>
                <button class="btn-logout" id="btn-logout" onclick="window.location.href='auth_logout.php'">Log Out</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content" id="main-content">
            <!-- Stats Cards -->
            <section class="stats-row" id="stats-section">
                <div class="stat-card" id="stat-active">
                    <p class="stat-label">Active Projects:</p>
                    <h2 class="stat-value" data-target="0" id="stat-active-val">0</h2>
                </div>
                <div class="stat-card" id="stat-ushers">
                    <p class="stat-label">Total Ushers Hired:</p>
                    <h2 class="stat-value" data-target="0" id="stat-ushers-val">0</h2>
                </div>
                <div class="stat-card" id="stat-events">
                    <p class="stat-label">Upcoming Events:</p>
                    <h2 class="stat-value" data-target="0" id="stat-events-val">0</h2>
                </div>
            </section>

            <!-- Revenue Analytics -->
            <section class="revenue-row">
                <div class="revenue-card">
                    <div class="revenue-card-icon revenue-icon-green"><i class="fas fa-chart-line"></i></div>
                    <div class="revenue-card-info">
                        <p class="revenue-label">Total Revenue</p>
                        <h3 class="revenue-value" id="rev-total">SAR 0</h3>
                        <span class="revenue-trend trend-up"><i class="fas fa-arrow-up"></i> 12.5% vs last month</span>
                    </div>
                </div>
                <div class="revenue-card">
                    <div class="revenue-card-icon revenue-icon-orange"><i class="fas fa-clock"></i></div>
                    <div class="revenue-card-info">
                        <p class="revenue-label">Pending Payments</p>
                        <h3 class="revenue-value" id="rev-pending">SAR 0</h3>
                        <span class="revenue-trend trend-neutral"><i class="fas fa-minus"></i> Loading...</span>
                    </div>
                </div>
                <div class="revenue-card">
                    <div class="revenue-card-icon revenue-icon-blue"><i class="fas fa-wallet"></i></div>
                    <div class="revenue-card-info">
                        <p class="revenue-label">Total Spent</p>
                        <h3 class="revenue-value" id="rev-spent">SAR 0</h3>
                        <span class="revenue-trend trend-down"><i class="fas fa-arrow-down"></i> Loading...</span>
                    </div>
                </div>
                <div class="revenue-card">
                    <div class="revenue-card-icon revenue-icon-purple"><i class="fas fa-user-tag"></i></div>
                    <div class="revenue-card-info">
                        <p class="revenue-label">Avg Cost / Usher</p>
                        <h3 class="revenue-value" id="rev-avg">SAR 0</h3>
                        <span class="revenue-trend trend-up"><i class="fas fa-arrow-up"></i> Efficiency</span>
                    </div>
                </div>
            </section>

            <!-- Revenue Chart + Recent Transactions -->
            <section class="dashboard-grid-2col">
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-chart-bar"></i> Monthly Revenue</h3>
                    <div class="chart-container">
                        <div class="bar-chart">
                            <div class="bar-group">
                                <div class="bar" style="height:45%"></div><span>Jan</span>
                            </div>
                            <div class="bar-group">
                                <div class="bar" style="height:62%"></div><span>Feb</span>
                            </div>
                            <div class="bar-group">
                                <div class="bar" style="height:38%"></div><span>Mar</span>
                            </div>
                            <div class="bar-group">
                                <div class="bar" style="height:75%"></div><span>Apr</span>
                            </div>
                            <div class="bar-group">
                                <div class="bar" style="height:58%"></div><span>May</span>
                            </div>
                            <div class="bar-group">
                                <div class="bar bar-accent" style="height:90%"></div><span>Jun</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-receipt"></i> Recent Transactions</h3>
                    <div class="transactions-list" id="transactions-list">
                        <p style="color: var(--gray-500); text-align:center; padding:2rem;">Loading transactions...</p>
                    </div>
                </div>
            </section>


            <!-- Filters -->
            <section class="filters-row" id="filters-section">
                <div class="filter-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search Projects..." id="search-input">
                </div>
                <div class="filter-date">
                    <input type="date" id="date-input" placeholder="mm/dd/yyyy">
                </div>
                <div class="filter-select">
                    <select id="status-filter">
                        <option value="">Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="filter-select">
                    <select id="location-filter">
                        <option value="">Location</option>
                        <option value="riyadh">Riyadh</option>
                        <option value="jeddah">Jeddah</option>
                        <option value="dubai">Dubai</option>
                    </select>
                </div>
            </section>

            <!-- Projects Section -->
            <section class="projects-section" id="projects-section">
                <h1 class="section-title">Active Projects</h1>

                <div class="projects-grid" id="projects-grid">
                    <!-- Cards will be generated by JS -->
                </div>

                <!-- Pagination -->
                <div class="pagination" id="pagination">
                    <button class="page-btn" id="prev-page"><i class="fas fa-chevron-left"></i></button>
                    <button class="page-btn active" data-page="1">1</button>
                    <button class="page-btn" data-page="2">2</button>
                    <button class="page-btn" data-page="3">3</button>
                    <button class="page-btn" id="next-page"><i class="fas fa-chevron-right"></i></button>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="footer-left">
            <h3>Wasla</h3>
            <p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p>
        </div>
        <div class="footer-links">
            <a href="terms.php">TERMS OF SERVICE</a>
            <a href="privacy.php">PRIVACY POLICY</a>
            <a href="contact.php">CONTACT US</a>
            <a href="#">TWITTER</a>
            <a href="#">INSTAGRAM</a>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
    // ===== SESSION-AWARE DASHBOARD LOADER =====
    (function() {
        const fmt = (n) => 'SAR ' + Number(n || 0).toLocaleString();

        fetch('api/client_dashboard.php')
            .then(res => res.json())
            .then(data => {
                if (!data.logged_in) {
                    window.location.href = 'login.php';
                    return;
                }

                // Update stat cards
                const activeEl = document.getElementById('stat-active-val');
                const ushersEl = document.getElementById('stat-ushers-val');
                const eventsEl = document.getElementById('stat-events-val');

                if (activeEl) { activeEl.setAttribute('data-target', data.active_projects || 0); activeEl.textContent = data.active_projects || 0; }
                if (ushersEl) { ushersEl.setAttribute('data-target', data.total_ushers_hired || 0); ushersEl.textContent = data.total_ushers_hired || 0; }
                if (eventsEl) { eventsEl.setAttribute('data-target', data.upcoming_events || 0); eventsEl.textContent = data.upcoming_events || 0; }

                // Re-run count-up animation
                if (typeof countUpAnimation === 'function') countUpAnimation();

                // Update revenue cards
                const revTotal = document.getElementById('rev-total');
                const revPending = document.getElementById('rev-pending');
                const revSpent = document.getElementById('rev-spent');
                const revAvg = document.getElementById('rev-avg');

                if (revTotal) revTotal.textContent = fmt(data.total_revenue);
                if (revPending) revPending.textContent = fmt(data.pending_payments);
                if (revSpent) revSpent.textContent = fmt(data.total_spent);
                if (revAvg) {
                    const avgCost = (data.total_ushers_hired > 0)
                        ? Math.round(data.total_spent / data.total_ushers_hired)
                        : 0;
                    revAvg.textContent = fmt(avgCost);
                }

                // Update transactions
                const txList = document.getElementById('transactions-list');
                if (txList && data.recent_transactions) {
                    if (data.recent_transactions.length === 0) {
                        txList.innerHTML = '<p style="color: var(--gray-500); text-align:center; padding:2rem;">No transactions yet</p>';
                    } else {
                        txList.innerHTML = data.recent_transactions.map(tx => {
                            const date = new Date(tx.created_at).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
                            const isExpense = tx.type === 'payout' || tx.type === 'payment';
                            const cls = isExpense ? 'tx-expense' : 'tx-income';
                            const icon = isExpense ? 'fa-arrow-up' : 'fa-arrow-down';
                            const sign = isExpense ? '-' : '+';
                            return `<div class="transaction-item">
                                <div class="transaction-icon ${cls}"><i class="fas ${icon}"></i></div>
                                <div class="transaction-info"><strong>${tx.description || 'Transaction'}</strong><span>${date}</span></div>
                                <span class="transaction-amount ${cls}">${sign} SAR ${Number(tx.amount).toLocaleString()}</span>
                            </div>`;
                        }).join('');
                    }
                }

                // Update project cards
                if (data.projects && data.projects.length > 0) {
                    const grid = document.getElementById('projects-grid');
                    if (grid) {
                        grid.innerHTML = '';
                        data.projects.forEach((project, index) => {
                            const card = document.createElement('div');
                            card.className = 'project-card';
                            const eventDate = new Date(project.event_date).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'});
                            const statusClass = project.status === 'active' ? 'status-active' : project.status === 'completed' ? 'status-completed' : 'status-pending';
                            const statusLabel = project.status.charAt(0).toUpperCase() + project.status.slice(1);
                            card.innerHTML = `
                                <div class="card-image">
                                    <img src="${project.image || 'images/event_gaming.png'}" alt="${project.title}" loading="lazy">
                                    <span class="card-badge ${statusClass}">${statusLabel}</span>
                                </div>
                                <div class="card-body">
                                    <h3 class="card-title">${project.title}</h3>
                                    <div class="card-detail"><i class="far fa-calendar"></i><span>${eventDate}</span></div>
                                    <div class="card-detail"><i class="fas fa-map-marker-alt"></i><span>${project.location}, ${project.city}</span></div>
                                    <div class="card-detail"><i class="fas fa-users"></i><span>${project.ushers_remaining} ushers remaining</span></div>
                                    <button class="btn-manage" onclick="window.location.href='projects.php'">Manage Project</button>
                                </div>
                            `;
                            grid.appendChild(card);
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(30px)';
                            setTimeout(() => {
                                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });
                    }
                }
            })
            .catch(err => {
                console.warn('Dashboard API error:', err);
            });

        // Session keep-alive check every 5 minutes
        setInterval(() => {
            fetch('api/session_check.php')
                .then(res => res.json())
                .then(data => {
                    if (!data.logged_in) {
                        alert('Your session has expired. Please log in again.');
                        window.location.href = 'login.php';
                    }
                })
                .catch(() => console.warn('Session check failed'));
        }, 5 * 60 * 1000);
    })();
    </script>
</body>

</html>