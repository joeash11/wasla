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
    <link rel="stylesheet" href="../styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../wasla-theme.js?v=<?= time() ?>"></script>
</head>
<body>
    <?php $active_page = 'profile'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content" id="main-content">
            <div class="profile-page">
                <div class="profile-header-card animate-fade-in-up">
                    <div class="profile-banner"></div>
                    <div class="profile-header-body">
                        <div class="profile-large-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="profile-header-info">
                            <h1 class="profile-full-name" id="profile-name">Loading...</h1>
                            <p class="profile-role"><i class="fas fa-id-badge"></i> Professional Usher</p>
                            <p class="profile-location-text" id="profile-location"><i class="fas fa-map-marker-alt"></i> ...</p>
                        </div>
                        <button class="btn-edit-profile" id="btn-edit-profile">
                            <i class="fas fa-pen"></i> Edit Profile
                        </button>
                    </div>
                </div>

                <div class="profile-grid">
                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.1s">
                        <h2 class="profile-card-title"><i class="fas fa-user"></i> About</h2>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Full Name</span>
                            <span class="profile-info-value" id="info-name">...</span>
                        </div>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Email</span>
                            <span class="profile-info-value" id="profile-email">...</span>
                        </div>
                        <div class="profile-info-row">
                            <span class="profile-info-label">Phone</span>
                            <span class="profile-info-value" id="profile-phone">...</span>
                        </div>
                    </div>

                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.2s">
                        <h2 class="profile-card-title"><i class="fas fa-align-left"></i> Bio</h2>
                        <p class="profile-bio-text" id="p-bio">No bio provided yet.</p>
                    </div>

                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.15s">
                        <h2 class="profile-card-title"><i class="fas fa-chart-bar"></i> Statistics</h2>
                        <div class="profile-stats-grid">
                            <div class="profile-stat-item">
                                <span class="profile-stat-number" id="p-gigs">--</span>
                                <span class="profile-stat-label">Completed Projects</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number" id="p-rating">--</span>
                                <span class="profile-stat-label">Avg Rating</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number" id="p-earned">--</span>
                                <span class="profile-stat-label">Total Earned</span>
                            </div>
                            <div class="profile-stat-item">
                                <span class="profile-stat-number" id="p-months">--</span>
                                <span class="profile-stat-label">Active Since</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.25s">
                        <h2 class="profile-card-title"><i class="fas fa-tools"></i> Skills</h2>
                        <div class="skills-tags" id="skills-list"></div>
                    </div>

                    <div class="profile-card animate-fade-in-up" style="animation-delay:0.3s">
                        <h2 class="profile-card-title"><i class="fas fa-star"></i> Recent Reviews</h2>
                        <div class="reviews-list" id="reviews-list"><p style="color:var(--gray-400)">Loading...</p></div>
                    </div>
                </div>
            </div>
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
                    <label class="form-label">Bio</label>
                    <textarea class="form-input form-textarea" id="edit-bio" rows="4"></textarea>
                </div>
                <div class="form-group" style="padding-bottom: 20px;">
                    <label class="form-label">Skills (comma separated)</label>
                    <input type="text" class="form-input" id="edit-skills" placeholder="e.g. Communication, Event Management, Languages">
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
                const fn = u.first_name + ' ' + u.last_name;
                
                document.getElementById('profile-name').textContent = fn;
                document.getElementById('info-name').textContent = fn;
                document.getElementById('profile-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${u.city || 'N/A'}`;
                document.getElementById('profile-email').textContent = u.email;
                document.getElementById('profile-phone').textContent = u.phone || 'N/A';
                
                if (u.bio) document.getElementById('p-bio').textContent = u.bio;

                // Populate modal
                document.getElementById('edit-first-name').value = u.first_name;
                document.getElementById('edit-last-name').value = u.last_name;
                document.getElementById('edit-email').value = u.email;
                document.getElementById('edit-phone').value = u.phone || '';
                document.getElementById('edit-location').value = u.city || '';
                document.getElementById('edit-bio').value = u.bio || '';
                document.getElementById('edit-skills').value = u.skills.join(', ') || '';

                document.getElementById('p-gigs').textContent = s.completed_gigs;
                document.getElementById('p-rating').innerHTML = `${s.avg_rating}`;
                document.getElementById('p-earned').textContent = 'EGP ' + Number(s.total_earned).toLocaleString();
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
                        <div class="review-item" style="padding-bottom:12px; margin-bottom:12px; border-bottom:1px solid #eee;">
                            <div class="review-header" style="display:flex; justify-content:space-between; align-items:center;">
                                <strong style="color:var(--gray-800)">${r.reviewer}</strong>
                                <span class="review-stars" style="color:#FFD700;">${'★'.repeat(r.rating)}${'☆'.repeat(5 - r.rating)}</span>
                            </div>
                            <p class="review-comment" style="color:var(--gray-600);font-size:0.95rem;margin:4px 0;">${r.comment || ''}</p>
                            <span class="review-date" style="color:var(--gray-400);font-size:0.8rem;">${r.date}</span>
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
            
            // Handle skills
            let skillsArray = document.getElementById('edit-skills').value.split(',').map(s => s.trim()).filter(s => s);

            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            try {
                const res = await fetch('../db/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ first_name, last_name, email, phone, city, bio, skills: skillsArray })
                });
                const result = await res.json();
                
                if (result.success) {
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved!';
                    saveBtn.style.background = 'var(--accent)';
                    
                    // Update DOM
                    const fn = first_name + ' ' + last_name;
                    document.getElementById('profile-name').textContent = fn;
                    document.getElementById('info-name').textContent = fn;
                    document.getElementById('profile-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${city || 'N/A'}`;
                    document.getElementById('profile-email').textContent = email;
                    document.getElementById('profile-phone').textContent = phone || 'N/A';
                    document.getElementById('p-bio').textContent = bio || 'No bio provided yet.';
                    document.querySelector('.profile-name').innerHTML = first_name + '<br>' + last_name;

                    const skillsList = document.getElementById('skills-list');
                    if (skillsArray.length > 0) {
                        skillsList.innerHTML = skillsArray.map(sk => `<span class="card-tag">${sk}</span>`).join('');
                    } else {
                        skillsList.innerHTML = '<span style="color:var(--gray-400)">No skills listed</span>';
                    }
                    
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
