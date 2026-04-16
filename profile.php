<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Profile</title>
    <meta name="description" content="View and edit your Wasla profile information.">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="theme-init.js"></script>
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
                <li><a href="dashboard.php" id="nav-dashboard">Dashboard</a></li>
                <li><a href="projects.php" id="nav-projects">My Projects</a></li>
                <li><a href="profile.php" class="active" id="nav-profile">Profile</a></li>
            </ul>
        </div>
        <div class="navbar-right">
            <span class="welcome-text">Welcome Abdullah</span>
            <a href="profile.php" class="user-avatar-small">
                <i class="fas fa-user-circle"></i>
            </a>
            <a href="create-project.php" class="btn-create">Create Project</a>
        </div>
    </nav>

    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-profile">
                <a href="profile.php" class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </a>
                <h3 class="profile-name">Abdullah<br>Elsayed</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-link" id="side-dashboard">
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
                <button class="btn-logout" id="btn-logout" onclick="window.location.href='auth_logout.php'">Log Out</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="content" id="main-content">
            <div class="profile-page">
                <!-- Profile Header Card -->
                <div class="profile-header-card animate-fade-in-up">
                    <div class="profile-banner"></div>
                    <div class="profile-header-body">
                        <div class="profile-large-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="profile-header-info">
                            <h1 class="profile-full-name">Abdullah Elsayed</h1>
                            <p class="profile-role"><i class="fas fa-briefcase"></i> Event Manager</p>
                            <p class="profile-location-text"><i class="fas fa-map-marker-alt"></i> Cairo, Egypt</p>
                        </div>
                        <button class="btn-edit-profile" id="btn-edit-profile">
                            <i class="fas fa-pen"></i> Edit Profile
                        </button>
                    </div>
                </div>

                <div class="profile-grid">
                    <!-- About Section -->
                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.1s">
                        <h2 class="profile-card-title"><i class="fas fa-user"></i> About</h2>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Full Name</span>
                            <span class="profile-info-value">Abdullah Elsayed</span>
                        </div>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Email</span>
                            <span class="profile-info-value">abdullah.elsayed@wasla.com</span>
                        </div>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Phone</span>
                            <span class="profile-info-value">+20 100 123 4567</span>
                        </div>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Member Since</span>
                            <span class="profile-info-value">January 2024</span>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.2s">
                        <h2 class="profile-card-title"><i class="fas fa-align-left"></i> Bio</h2>
                        <p class="profile-bio-text">
                            Experienced event manager with a passion for creating seamless, large-scale experiences. 
                            Specializing in gaming festivals, corporate events, and training programs across the MENA region. 
                            Always looking for talented ushers and coordinators to build world-class event teams.
                        </p>
                    </div>

                    <!-- Stats Section -->
                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.15s">
                        <h2 class="profile-card-title"><i class="fas fa-chart-bar"></i> Statistics</h2>
                        <div class="profile-stats-grid">
                            <div class="profile-stat-item">
                                <span class="profile-stat-number">6</span>
                                <span class="profile-stat-label">Total Projects</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number">2</span>
                                <span class="profile-stat-label">Active</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number">15</span>
                                <span class="profile-stat-label">Ushers Hired</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number">4.8</span>
                                <span class="profile-stat-label">Rating</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.25s">
                        <h2 class="profile-card-title"><i class="fas fa-clock"></i> Recent Activity</h2>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon activity-icon-green"><i class="fas fa-plus"></i></div>
                                <div class="activity-body">
                                    <p class="activity-text">Created project <strong>"Insomnia Egypt Gaming Festival 2026"</strong></p>
                                    <span class="activity-time">2 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon activity-icon-blue"><i class="fas fa-user-plus"></i></div>
                                <div class="activity-body">
                                    <p class="activity-text">Hired <strong>3 new ushers</strong> for Gaming Festival</p>
                                    <span class="activity-time">5 hours ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon activity-icon-purple"><i class="fas fa-check-circle"></i></div>
                                <div class="activity-body">
                                    <p class="activity-text">Completed project <strong>"Foundation Training Year"</strong></p>
                                    <span class="activity-time">1 day ago</span>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon activity-icon-orange"><i class="fas fa-edit"></i></div>
                                <div class="activity-body">
                                    <p class="activity-text">Updated profile information</p>
                                    <span class="activity-time">3 days ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer" id="footer">
        <div class="footer-left">
            <h3>Wasla</h3>
            <p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p>
        </div>
        <div class="footer-links">
<<<<<<< HEAD:profile.html
            <a href="terms.html">TERMS OF SERVICE</a>
            <a href="privacy.html">PRIVACY POLICY</a>
            <a href="contact.html">CONTACT US</a>
=======
            <a href="terms.php">TERMS OF SERVICE</a>
            <a href="privacy.php">PRIVACY POLICY</a>
            <a href="contact.php">CONTACT US</a>
            <a href="#">TWITTER</a>
            <a href="#">INSTAGRAM</a>
>>>>>>> fdded6d (kosomk):profile.php
        </div>
    </footer>

    <!-- Edit Profile Modal -->
    <div class="modal-overlay" id="edit-profile-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                <button class="modal-close" id="modal-close-profile">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-input" id="edit-first-name" value="Abdullah">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-input" id="edit-last-name" value="Elsayed">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="edit-email" value="abdullah.elsayed@wasla.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-input" id="edit-phone" value="+20 100 123 4567">
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" class="form-input" id="edit-location" value="Cairo, Egypt">
                </div>
                <div class="form-group">
                    <label class="form-label">Bio</label>
                    <textarea class="form-input form-textarea" id="edit-bio" rows="4">Experienced event manager with a passion for creating seamless, large-scale experiences. Specializing in gaming festivals, corporate events, and training programs across the MENA region. Always looking for talented ushers and coordinators to build world-class event teams.</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" id="btn-cancel-profile">Cancel</button>
                <button class="btn-modal-save" id="btn-save-profile"><i class="fas fa-check"></i> Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        // Edit Profile Modal
        const editModal = document.getElementById('edit-profile-modal');
        const editBtn = document.getElementById('btn-edit-profile');
        const closeBtn = document.getElementById('modal-close-profile');
        const cancelBtn = document.getElementById('btn-cancel-profile');
        const saveBtn = document.getElementById('btn-save-profile');

        function openEditModal() {
            editModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModal() {
            editModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        editBtn.addEventListener('click', openEditModal);
        closeBtn.addEventListener('click', closeEditModal);
        cancelBtn.addEventListener('click', closeEditModal);
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) closeEditModal();
        });

        saveBtn.addEventListener('click', async () => {
            const firstName = document.getElementById('edit-first-name').value;
            const lastName = document.getElementById('edit-last-name').value;
            const email = document.getElementById('edit-email').value;
            const phone = document.getElementById('edit-phone').value;
            const location = document.getElementById('edit-location').value;
            const bio = document.getElementById('edit-bio').value;

            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Save to backend
            try {
                await fetch('db/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ first_name: firstName, last_name: lastName, email, phone, bio, city: location })
                });
            } catch(e) { console.log('Profile saved locally only'); }

            // Update displayed profile values
            document.querySelector('.profile-full-name').textContent = firstName + ' ' + lastName;
            document.querySelectorAll('.profile-info-value')[0].textContent = firstName + ' ' + lastName;
            document.querySelectorAll('.profile-info-value')[1].textContent = email;
            document.querySelectorAll('.profile-info-value')[2].textContent = phone;
            document.querySelector('.profile-location-text').innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + location;
            document.querySelector('.profile-bio-text').textContent = bio;
            document.querySelector('.welcome-text').textContent = 'Welcome ' + firstName;
            document.querySelector('.profile-name').innerHTML = firstName + '<br>' + lastName;

            saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
            saveBtn.style.background = 'var(--accent)';
            saveBtn.disabled = false;
            setTimeout(() => {
                saveBtn.innerHTML = '<i class="fas fa-check"></i> Save Changes';
                saveBtn.style.background = '';
                closeEditModal();
            }, 1200);
        });

        // ===== Load Profile from Backend =====
        async function loadProfile() {
            try {
                const res = await fetch('db/get_user.php');
                const data = await res.json();
                if (data.logged_in && data.user) {
                    const u = data.user;
                    document.querySelector('.profile-full-name').textContent = u.first_name + ' ' + u.last_name;
                    document.querySelector('.welcome-text').textContent = 'Welcome ' + u.first_name;
                    document.querySelector('.profile-name').innerHTML = u.first_name + '<br>' + u.last_name;
                    document.querySelector('.profile-role').innerHTML = '<i class="fas fa-briefcase"></i> ' + (u.role === 'client' ? 'Event Manager' : 'Usher');
                    document.querySelector('.profile-location-text').innerHTML = '<i class="fas fa-map-marker-alt"></i> ' + (u.city || 'Cairo, Egypt');

                    const infoValues = document.querySelectorAll('.profile-info-value');
                    if (infoValues[0]) infoValues[0].textContent = u.first_name + ' ' + u.last_name;
                    if (infoValues[1]) infoValues[1].textContent = u.email;
                    if (infoValues[2]) infoValues[2].textContent = u.phone || '+20 100 123 4567';
                    if (infoValues[3]) infoValues[3].textContent = new Date(u.created_at).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

                    if (u.bio) document.querySelector('.profile-bio-text').textContent = u.bio;

                    // Populate edit modal
                    document.getElementById('edit-first-name').value = u.first_name;
                    document.getElementById('edit-last-name').value = u.last_name;
                    document.getElementById('edit-email').value = u.email;
                    document.getElementById('edit-phone').value = u.phone || '';
                    document.getElementById('edit-location').value = u.city || 'Cairo, Egypt';
                    if (u.bio) document.getElementById('edit-bio').value = u.bio;
                }
            } catch(e) { console.log('Profile: Using static data'); }
        }

        // Logout
        document.querySelector('.btn-logout')?.addEventListener('click', async () => {
            try { await fetch('db/logout.php'); } catch(e) {}
            window.location.href = 'landing.html';
        });

        loadProfile();
    </script>
</html>
