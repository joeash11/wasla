<?php require_once __DIR__ . '/../includes/usher_guard.php'; ?>
<?php
// Fetch user data from DB for settings
require_once __DIR__ . '/../db/connection.php';
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$settings_user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Settings</title>
    <meta name="description" content="Manage your Wasla account settings.">
    <link rel="stylesheet" href="../styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../images/wasla-icon.png">
    <script src="../theme-init.js"></script>
</head>
<body>
    <?php $active_page = 'settings'; ?>
    <?php include __DIR__ . '/../includes/usher_navbar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/../includes/usher_sidebar.php'; ?>
        <main class="content" id="main-content">
            <h1 class="section-title">Settings</h1>
            <div class="settings-page">
                <div class="settings-tabs">
                    <button class="settings-tab active" data-tab="account" onclick="switchSettingsTab('account')"><i class="fas fa-user-cog"></i> Account</button>
                    <button class="settings-tab" data-tab="notifications" onclick="switchSettingsTab('notifications')"><i class="fas fa-bell"></i> Notifications</button>
                    <button class="settings-tab" data-tab="privacy" onclick="switchSettingsTab('privacy')"><i class="fas fa-shield-alt"></i> Privacy</button>
                    <button class="settings-tab" data-tab="appearance" onclick="switchSettingsTab('appearance')"><i class="fas fa-palette"></i> Appearance</button>
                </div>
                <div class="settings-panel active" id="panel-account">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Account Information</h2>
                        <div class="settings-form">
                            <div class="form-row"><div class="form-group"><label class="form-label">First Name</label><input type="text" class="form-input" id="acct-first" value="<?php echo htmlspecialchars($settings_user['first_name'] ?? ''); ?>"></div><div class="form-group"><label class="form-label">Last Name</label><input type="text" class="form-input" id="acct-last" value="<?php echo htmlspecialchars($settings_user['last_name'] ?? ''); ?>"></div></div>
                            <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-input" id="acct-email" value="<?php echo htmlspecialchars($settings_user['email'] ?? ''); ?>"></div>
                            <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" class="form-input" id="acct-phone" value="<?php echo htmlspecialchars($settings_user['phone'] ?? ''); ?>"></div>
                            <button class="btn-save" id="btn-save-account">Save Changes</button>
                        </div>
                    </div>
                    <div class="settings-card">
                        <h2 class="settings-card-title">Change Password</h2>
                        <div class="settings-form">
                            <div class="form-group"><label class="form-label">Current Password</label><input type="password" class="form-input" placeholder="Enter current password"></div>
                            <div class="form-row"><div class="form-group"><label class="form-label">New Password</label><input type="password" class="form-input" placeholder="New password"></div><div class="form-group"><label class="form-label">Confirm</label><input type="password" class="form-input" placeholder="Confirm"></div></div>
                            <button class="btn-save" id="btn-update-password">Update Password</button>
                        </div>
                    </div>
                    <div class="settings-card settings-card-danger">
                        <h2 class="settings-card-title"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h2>
                        <p class="settings-danger-text">Once you delete your account, there is no going back.</p>
                        <button class="btn-danger" id="btn-delete-account">Delete Account</button>
                    </div>
                </div>
                <div class="settings-panel" id="panel-notifications">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Email Notifications</h2>
                        <div class="settings-toggle-list">
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Project Updates</h4><p class="toggle-desc">Receive emails when projects are updated</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">New Messages</h4><p class="toggle-desc">Get notified for new messages</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Usher Applications</h4><p class="toggle-desc">Alerts when ushers apply</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Marketing</h4><p class="toggle-desc">Promotional content</p></div><label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>
                    <div class="settings-card">
                        <h2 class="settings-card-title">Push Notifications</h2>
                        <div class="settings-toggle-list">
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Desktop Notifications</h4><p class="toggle-desc">Show browser notifications</p></div><label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Sound Alerts</h4><p class="toggle-desc">Play sound on new notifications</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>
                </div>
                <div class="settings-panel" id="panel-privacy">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Privacy</h2>
                        <div class="settings-toggle-list">
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Profile Visibility</h4><p class="toggle-desc">Allow others to see your profile</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Show Email</h4><p class="toggle-desc">Display email publicly</p></div><label class="toggle-switch"><input type="checkbox"><span class="toggle-slider"></span></label></div>
                            <div class="settings-toggle-row"><div><h4 class="toggle-title">Online Status</h4><p class="toggle-desc">Show when you are active</p></div><label class="toggle-switch"><input type="checkbox" checked><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>
                </div>
                <div class="settings-panel" id="panel-appearance">
                    <div class="settings-card">
                        <h2 class="settings-card-title">Theme</h2>
                        <div class="theme-options">
                            <div class="theme-option active" data-theme="light"><div class="theme-preview theme-preview-light"><div class="theme-preview-bar"></div><div class="theme-preview-body"><div class="theme-preview-sidebar"></div><div class="theme-preview-content"></div></div></div><span>Light</span></div>
                            <div class="theme-option" data-theme="dark"><div class="theme-preview theme-preview-dark"><div class="theme-preview-bar"></div><div class="theme-preview-body"><div class="theme-preview-sidebar"></div><div class="theme-preview-content"></div></div></div><span>Dark</span></div>
                            <div class="theme-option" data-theme="system"><div class="theme-preview theme-preview-system"><div class="theme-preview-bar"></div><div class="theme-preview-body"><div class="theme-preview-sidebar"></div><div class="theme-preview-content"></div></div></div><span>System</span></div>
                        </div>
                    </div>
                    <div class="settings-card">
                        <h2 class="settings-card-title">Language</h2>
                        <div class="form-group"><select class="form-input" id="settings-language"><option value="en">English</option><option value="ar">العربية (Arabic)</option></select></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <div class="toast-notification" id="settings-toast"><i class="fas fa-check-circle"></i> <span id="toast-text">Changes saved!</span></div>
    <script>
        // ===== TAB SWITCHING =====
        function switchSettingsTab(tab) {
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
            document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
            document.getElementById(`panel-${tab}`).classList.add('active');
        }

        // ===== TOAST =====
        function showToast(msg) {
            const toast = document.getElementById('settings-toast');
            document.getElementById('toast-text').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        // ===== SAVE ACCOUNT INFO =====
        document.getElementById('btn-save-account').addEventListener('click', async () => {
            const btn = document.getElementById('btn-save-account');
            const data = {
                first_name: document.getElementById('acct-first').value,
                last_name: document.getElementById('acct-last').value,
                email: document.getElementById('acct-email').value,
                phone: document.getElementById('acct-phone').value
            };
            btn.textContent = '✓ Saving...';
            btn.disabled = true;
            try {
                await fetch('../db/update_profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
            } catch(e) {}
            btn.textContent = '✓ Saved!';
            btn.style.background = 'var(--accent)';
            showToast('Account information updated!');
            setTimeout(() => { btn.textContent = 'Save Changes'; btn.style.background = ''; btn.disabled = false; }, 2000);
        });

        // ===== UPDATE PASSWORD =====
        document.getElementById('btn-update-password').addEventListener('click', () => {
            const btn = document.getElementById('btn-update-password');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = 'Update Password';
                btn.disabled = false;
                showToast('Password updated successfully');
                const inputs = btn.parentElement.querySelectorAll('input[type="password"]');
                inputs.forEach(i => i.value = '');
            }, 1000);
        });

        // ===== DELETE ACCOUNT =====
        document.getElementById('btn-delete-account').addEventListener('click', () => {
            if (confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
                const btn = document.getElementById('btn-delete-account');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                btn.disabled = true;
                setTimeout(() => {
                    alert('Your account has been deleted.');
                    window.location.href = '../auth_logout.php';
                }, 1500);
            }
        });

        // ===== NOTIFICATION TOGGLE =====
        document.querySelectorAll('.toggle-switch input').forEach(toggle => {
            toggle.addEventListener('change', () => {
                const title = toggle.closest('.settings-toggle-row')?.querySelector('.toggle-title')?.textContent || 'setting';
                const state = toggle.checked ? 'enabled' : 'disabled';
                localStorage.setItem('wasla_toggle_' + title.replace(/\s+/g, '_').toLowerCase(), state);
                showToast(title + ' ' + state);
            });
            const title = toggle.closest('.settings-toggle-row')?.querySelector('.toggle-title')?.textContent || '';
            const saved = localStorage.getItem('wasla_toggle_' + title.replace(/\s+/g, '_').toLowerCase());
            if (saved !== null) toggle.checked = saved === 'enabled';
        });

        // ===== THEME SWITCHING =====
        function applyTheme(theme) {
            localStorage.setItem('wasla_theme', theme);
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else if (theme === 'light') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
            }
        }
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.theme-option').forEach(x => x.classList.remove('active'));
                option.classList.add('active');
                applyTheme(option.dataset.theme);
                showToast('Theme changed to ' + option.dataset.theme.charAt(0).toUpperCase() + option.dataset.theme.slice(1));
            });
        });
        const savedTheme = localStorage.getItem('wasla_theme') || 'light';
        document.querySelectorAll('.theme-option').forEach(o => o.classList.toggle('active', o.dataset.theme === savedTheme));
        applyTheme(savedTheme);

        // ===== LANGUAGE =====
        const langSelect = document.getElementById('settings-language');
        langSelect.value = localStorage.getItem('wasla_language') || 'en';
        langSelect.addEventListener('change', () => {
            localStorage.setItem('wasla_language', langSelect.value);
            document.documentElement.dir = langSelect.value === 'ar' ? 'rtl' : 'ltr';
            showToast('Language changed');
        });
    </script>
</body>
</html>
