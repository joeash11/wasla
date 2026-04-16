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
    <title>Wasla - My Profile</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../theme-init.js"></script>
</head>
<body>
    <?php $active_page = 'profile'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content">
            <h1 class="section-title">My Profile</h1>
            <!-- Profile Header -->
            <section class="profile-header-card">
                <div class="profile-top">
                    <div class="profile-large-avatar"><i class="fas fa-user-circle"></i></div>
                    <div class="profile-info" style="display:flex; flex-direction:column; gap:6px; flex-grow: 1;">
                        <h1 id="profile-name" style="font-size:2rem; font-weight:800; color:var(--primary); margin:0;">Loading...</h1>
                        <p class="profile-role" style="font-size:1.1rem; color:var(--gray-600); font-weight:600; margin:0;"><i class="fas fa-id-badge"></i> Professional Usher</p>
                        <p class="profile-location" id="profile-location" style="color:var(--gray-600); font-weight:500; margin:0;"><i class="fas fa-map-marker-alt"></i> ...</p>
                        <div style="display:flex; gap:16px; margin-top:4px;">
                            <p class="profile-email" id="profile-email" style="color:var(--gray-600); font-weight:500; margin:0;"><i class="fas fa-envelope"></i> ...</p>
                            <p class="profile-phone" id="profile-phone" style="color:var(--gray-600); font-weight:500; margin:0;"><i class="fas fa-phone"></i> ...</p>
                        </div>
                    </div>
                    <button class="btn-edit-profile" id="btn-edit-profile" style="margin-left: auto; align-self: flex-start;">
                        <i class="fas fa-pen"></i> Edit Profile
                    </button>
                </div>
            </section>
            <!-- Stats -->
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Completed Projects</p><h2 class="stat-value" id="p-gigs">--</h2></div>
                <div class="stat-card"><p class="stat-label">Avg Rating</p><h2 class="stat-value" id="p-rating">--</h2></div>
                <div class="stat-card"><p class="stat-label">Total Earned</p><h2 class="stat-value" id="p-earned">--</h2></div>
                <div class="stat-card"><p class="stat-label">Active Since</p><h2 class="stat-value" id="p-months">--</h2></div>
            </section>
            <section class="dashboard-grid-2col">
                <!-- Skills -->
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-tools"></i> Skills</h3>
                    <div class="skills-tags" id="skills-list"><span class="card-tag">Loading...</span></div>
                </div>
                <!-- Reviews -->
                <div class="dashboard-panel">
                    <h3 class="panel-title"><i class="fas fa-star"></i> Recent Reviews</h3>
                    <div class="reviews-list" id="reviews-list"><p style="color:var(--gray-400)">Loading...</p></div>
                </div>
            </section>
        </main>
    </div>

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
                        <input type="text" class="form-input" id="edit-first-name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-input" id="edit-last-name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="edit-email">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-input" id="edit-phone">
                </div>
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-input" id="edit-location">
                </div>
                <div class="form-group">
                    <label class="form-label">Bio (Optional)</label>
                    <textarea class="form-input form-textarea" id="edit-bio" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal-cancel" id="btn-cancel-profile">Cancel</button>
                <button class="btn-modal-save" id="btn-save-profile"><i class="fas fa-check"></i> Save Changes</button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
    const USHER_ID = <?php echo $usher_id; ?>;
    document.addEventListener('DOMContentLoaded', () => {
        fetch(`../api/usher_profile.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const u = data.user;
                const s = data.stats;
                document.getElementById('profile-name').textContent = u.first_name + ' ' + u.last_name;
                document.getElementById('profile-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${u.city || 'N/A'}`;
                document.getElementById('profile-email').innerHTML = `<i class="fas fa-envelope"></i> ${u.email}`;
                document.getElementById('profile-phone').innerHTML = `<i class="fas fa-phone"></i> ${u.phone || 'N/A'}`;

                // Populate modal
                document.getElementById('edit-first-name').value = u.first_name;
                document.getElementById('edit-last-name').value = u.last_name;
                document.getElementById('edit-email').value = u.email;
                document.getElementById('edit-phone').value = u.phone || '';
                document.getElementById('edit-location').value = u.city || '';
                document.getElementById('edit-bio').value = u.bio || '';

                document.getElementById('p-gigs').textContent = s.completed_gigs;
                document.getElementById('p-rating').innerHTML = `${s.avg_rating} <small style="font-size:0.6em;color:var(--gray-400)">/ 5</small>`;
                document.getElementById('p-earned').textContent = 'SAR ' + Number(s.total_earned).toLocaleString();
                document.getElementById('p-months').textContent = s.months_active + ' months';

                // Skills
                const skillsList = document.getElementById('skills-list');
                if (u.skills.length > 0) {
                    skillsList.innerHTML = u.skills.map(sk => `<span class="card-tag">${sk}</span>`).join('');
                } else {
                    skillsList.innerHTML = '<span style="color:var(--gray-400)">No skills listed</span>';
                }

                // Reviews
                const reviewsList = document.getElementById('reviews-list');
                if (data.reviews.length === 0) {
                    reviewsList.innerHTML = '<p style="color:var(--gray-400)">No reviews yet</p>';
                } else {
                    reviewsList.innerHTML = data.reviews.map(r => `
                        <div class="review-item">
                            <div class="review-header">
                                <strong>${r.reviewer}</strong>
                                <span class="review-stars">${'★'.repeat(r.rating)}${'☆'.repeat(5 - r.rating)}</span>
                            </div>
                            <p class="review-comment">${r.comment || ''}</p>
                            <span class="review-date">${r.date}</span>
                        </div>
                    `).join('');
                }
            });

        // Edit Profile Modal Logic
        const editModal = document.getElementById('edit-profile-modal');
        const editBtn = document.getElementById('btn-edit-profile');
        const closeBtn = document.getElementById('modal-close-profile');
        const cancelBtn = document.getElementById('btn-cancel-profile');
        const saveBtn = document.getElementById('btn-save-profile');

        const openModal = () => { editModal.classList.add('active'); document.body.style.overflow = 'hidden'; };
        const closeModal = () => { editModal.classList.remove('active'); document.body.style.overflow = ''; };

        editBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        editModal.addEventListener('click', (e) => { if (e.target === editModal) closeModal(); });

        saveBtn.addEventListener('click', async () => {
            const first_name = document.getElementById('edit-first-name').value;
            const last_name = document.getElementById('edit-last-name').value;
            const email = document.getElementById('edit-email').value;
            const phone = document.getElementById('edit-phone').value;
            const city = document.getElementById('edit-location').value;
            const bio = document.getElementById('edit-bio').value;

            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            try {
                const res = await fetch('../db/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ first_name, last_name, email, phone, city, bio })
                });
                const result = await res.json();
                
                if (result.success) {
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
                    saveBtn.style.background = 'var(--accent)';
                    
                    // Update DOM
                    document.getElementById('profile-name').textContent = first_name + ' ' + last_name;
                    document.getElementById('profile-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${city || 'N/A'}`;
                    document.getElementById('profile-email').innerHTML = `<i class="fas fa-envelope"></i> ${email}`;
                    document.getElementById('profile-phone').innerHTML = `<i class="fas fa-phone"></i> ${phone || 'N/A'}`;
                    document.querySelector('.profile-name').innerHTML = first_name + '<br>' + last_name;
                    document.querySelector('.welcome-text').textContent = 'Welcome ' + first_name;
                    
                    setTimeout(() => {
                        saveBtn.innerHTML = '<i class="fas fa-check"></i> Save Changes';
                        saveBtn.style.background = '';
                        closeModal();
                    }, 1200);
                } else {
                    alert('Error saving profile');
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> Save Changes';
                }
            } catch (err) {
                console.error(err);
                saveBtn.innerHTML = '<i class="fas fa-check"></i> Save Changes';
            }
            saveBtn.disabled = false;
        });
    });
    </script>
</body>
</html>
