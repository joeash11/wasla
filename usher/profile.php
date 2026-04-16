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
                    <div class="profile-info">
                        <h2 id="profile-name">Loading...</h2>
                        <p class="profile-location" id="profile-location"><i class="fas fa-map-marker-alt"></i> ...</p>
                        <p class="profile-email" id="profile-email"><i class="fas fa-envelope"></i> ...</p>
                        <p class="profile-phone" id="profile-phone"><i class="fas fa-phone"></i> ...</p>
                    </div>
                </div>
            </section>
            <!-- Stats -->
            <section class="stats-row">
                <div class="stat-card"><p class="stat-label">Completed Gigs</p><h2 class="stat-value" id="p-gigs">--</h2></div>
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
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
    const USHER_ID = <?php echo $usher_id; ?>;
    document.addEventListener('DOMContentLoaded', () => {
        fetch(`/wasla/api/usher_profile.php?usher_id=${USHER_ID}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const u = data.user;
                const s = data.stats;
                document.getElementById('profile-name').textContent = u.first_name + ' ' + u.last_name;
                document.getElementById('profile-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${u.city || 'N/A'}`;
                document.getElementById('profile-email').innerHTML = `<i class="fas fa-envelope"></i> ${u.email}`;
                document.getElementById('profile-phone').innerHTML = `<i class="fas fa-phone"></i> ${u.phone || 'N/A'}`;

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
    });
    </script>
</body>
</html>
