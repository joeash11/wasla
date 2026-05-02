<?php require_once __DIR__ . '/includes/client_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Profile</title>
    <meta name="description" content="View and edit your Wasla profile information.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body>
    <?php $active_page = 'profile'; ?>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="main-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="content" id="main-content">
            <div class="profile-page">
                <!-- Profile Header Card -->
                <div class="profile-header-card animate-fade-in-up">
                    <div class="profile-banner"></div>
                    <div class="profile-header-body">
                        <div class="profile-large-avatar" id="profile-avatar-display">
                            <i class="fas fa-user-circle" id="avatar-icon"></i>
                            <img src="" alt="Profile" class="profile-avatar-img" id="avatar-img" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:50%;">
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

    <?php include __DIR__ . '/includes/footer.php'; ?>

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
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-camera"></i> Profile Photo</label>
                    <input type="file" class="form-input" id="edit-profile-image" accept="image/jpeg,image/png,image/gif,image/webp" style="padding:10px;">
                    <small style="color:rgba(255,255,255,0.4);font-size:0.78rem;">Max 5MB. JPG, PNG, GIF, or WebP.</small>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-file-pdf"></i> Upload CV</label>
                    <input type="file" class="form-input" id="edit-cv" accept=".pdf,.doc,.docx" style="padding:10px;">
                    <small style="color:rgba(255,255,255,0.4);font-size:0.78rem;">PDF, DOC, or DOCX format.</small>
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
            const profileImageFile = document.getElementById('edit-profile-image').files[0];
            const cvFile = document.getElementById('edit-cv').files[0];

            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Use FormData to support file uploads
            const formData = new FormData();
            formData.append('first_name', firstName);
            formData.append('last_name', lastName);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('city', location);
            formData.append('bio', bio);
            if (profileImageFile) formData.append('profile_image', profileImageFile);
            if (cvFile) formData.append('cv', cvFile);

            try {
                const res = await fetch('db/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.profile_image) {
                    document.getElementById('avatar-img').src = data.profile_image;
                    document.getElementById('avatar-img').style.display = 'block';
                    document.getElementById('avatar-icon').style.display = 'none';
                }
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

                    // Show profile image if exists
                    if (u.profile_image) {
                        document.getElementById('avatar-img').src = u.profile_image;
                        document.getElementById('avatar-img').style.display = 'block';
                        document.getElementById('avatar-icon').style.display = 'none';
                    }

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
            window.location.href = 'index.php';
        });

        loadProfile();
    </script>
</html>
