<?php require_once __DIR__ . '/../includes/usher_guard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Help Center</title>
    <meta name="description" content="Get help and support for Wasla.">
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../theme-init.js"></script>
</head>
<body>
    <?php $active_page = 'help'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content" id="main-content">
            <h1 class="section-title">Help Center</h1>
            <!-- Help Search -->
            <div class="help-search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for help..." id="help-search" class="help-search-input">
            </div>
            <!-- Quick Links -->
            <div class="help-quick-links">
                <a href="#faq" class="help-quick-card"><i class="fas fa-book"></i><span>FAQ</span></a>
                <a href="#contact" class="help-quick-card"><i class="fas fa-headset"></i><span>Contact Support</span></a>
                <a href="#guides" class="help-quick-card"><i class="fas fa-play-circle"></i><span>Guides</span></a>
                <a href="#community" class="help-quick-card"><i class="fas fa-users"></i><span>Community</span></a>
            </div>
            <!-- FAQ Section -->
            <section id="faq" class="help-section">
                <h2 class="help-section-title">Frequently Asked Questions</h2>
                <div class="faq-list" id="faq-list">
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>How do I create a new project?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>Click the "Create Project" button in the top navbar or navigate to My Projects and click the create button. Fill in the project details, set usher requirements, and submit.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>How do I hire ushers for my event?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>After creating a project, ushers can apply to your event. You can review applications from the project management page and accept or decline candidates.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>Can I edit a project after creating it?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>Yes! Go to My Projects, click "Manage Project" on the card you want to edit, and you'll be able to update all project details including date, location, and usher requirements.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>How does the messaging system work?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>You can communicate with ushers and team members through the Messages page. Click on a conversation to view and send messages in real-time.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>How do I change my account settings?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>Navigate to Settings from the sidebar. You can update your account information, password, notification preferences, privacy settings, and appearance.</p></div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question" onclick="toggleFaq(this)"><span>What payment methods are accepted?</span><i class="fas fa-chevron-down"></i></button>
                        <div class="faq-answer"><p>Wasla accepts major credit cards (Visa, Mastercard, Amex), bank transfers, and digital wallets including Apple Pay and mada.</p></div>
                    </div>
                </div>
            </section>
            <!-- Getting Started Guides -->
            <section id="guides" class="help-section">
                <h2 class="help-section-title">Getting Started</h2>
                <div class="guides-grid">
                    <div class="guide-card" style="cursor:pointer" onclick="window.location.href='#faq'"><div class="guide-icon"><i class="fas fa-rocket"></i></div><h3>Quick Start Guide</h3><p>Learn the basics of Wasla in 5 minutes</p></div>
                    <div class="guide-card" style="cursor:pointer" onclick="window.location.href='#faq'; setTimeout(() => document.querySelectorAll('.faq-item button')[2].click(), 300);"><div class="guide-icon"><i class="fas fa-project-diagram"></i></div><h3>Managing Projects</h3><p>Best practices for project management</p></div>
                    <div class="guide-card" style="cursor:pointer" onclick="window.location.href='#faq'; setTimeout(() => document.querySelectorAll('.faq-item button')[1].click(), 300);"><div class="guide-icon"><i class="fas fa-user-check"></i></div><h3>Working with Ushers</h3><p>How to find and hire the best ushers</p></div>
                </div>
            </section>
            <!-- Contact Support -->
            <section id="contact" class="help-section">
                <h2 class="help-section-title">Contact Support</h2>
                <div class="settings-card">
                    <div class="settings-form">
                        <div class="form-group"><label class="form-label">Subject</label><input type="text" class="form-input" placeholder="What do you need help with?"></div>
                        <div class="form-group"><label class="form-label">Category</label>
                            <select class="form-input"><option>General</option><option>Account Issues</option><option>Billing</option><option>Technical Problem</option><option>Feature Request</option></select>
                        </div>
                        <div class="form-group"><label class="form-label">Message</label><textarea class="form-input form-textarea" rows="5" placeholder="Describe your issue in detail..."></textarea></div>
                        <button class="btn-save" onclick="this.textContent='✓ Sent!';this.style.background='var(--accent)';setTimeout(()=>{ alert('Your message have been delivered to our customer services and our staff is going to text you back soon.'); window.location.href='dashboard.php'; }, 500)">Send Message</button>
                    </div>
                </div>
            </section>
        </main>
    </div>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script>
        function toggleFaq(btn){
            const item=btn.parentElement;
            const isOpen=item.classList.contains('open');
            document.querySelectorAll('.faq-item').forEach(i=>i.classList.remove('open'));
            if(!isOpen) item.classList.add('open');
        }
        document.getElementById('help-search').addEventListener('input',e=>{
            const q=e.target.value.toLowerCase();
            document.querySelectorAll('.faq-item').forEach(item=>{
                const text=item.querySelector('.faq-question span').textContent.toLowerCase();
                item.style.display=text.includes(q)?'':'none';
            });
        });
    </script>
</body>
</html>
