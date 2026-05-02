<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Sign Up</title>
    <meta name="description" content="Create your Wasla account and start connecting with events and talent.">
    <link rel="stylesheet" href="styles.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
    <script src="wasla-theme.js"></script>
</head>
<body class="auth-body page-transition">
    <div class="auth-wrapper">
        <div class="auth-left">
            <div class="auth-left-content">
                <a href="index.php" class="logo" translate="no">
                    <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="48" height="48">
                    <span class="logo-text" style="color:#fff;font-size:1.6rem" translate="no">Wasla</span>
                </a>
                <h1 class="auth-hero-title">Join the Future<br>of <span class="hero-gradient-text">Event Staffing</span></h1>
                <p class="auth-hero-desc">Whether you manage events or work as an usher, Wasla is your one-stop platform.</p>
                <div class="auth-hero-stats">
                    <div class="auth-hero-stat"><i class="fas fa-bolt"></i> Set up in under 2 minutes</div>
                    <div class="auth-hero-stat"><i class="fas fa-shield-alt"></i> Verified & Secure</div>
                    <div class="auth-hero-stat"><i class="fas fa-globe"></i> Available across MENA</div>
                </div>
            </div>
        </div>
        <div class="auth-right">
            <div class="auth-form-container">
                <h2 class="auth-form-title">Create Your Account</h2>
                <p class="auth-form-subtitle">Select your role and get started</p>
                <!-- Error Messages -->
                <div class="auth-error" id="auth-error" style="display:none">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="error-text"></span>
                </div>
                <!-- Role Slide Toggle -->
                <div class="auth-slide-toggle" id="role-toggle">
                    <span class="toggle-option active" data-role="client" onclick="switchSignupRole(this)">Client</span>
                    <span class="toggle-option" data-role="usher" onclick="switchSignupRole(this)">Usher</span>
                    <div class="toggle-knob"></div>
                </div>
                <!-- Client Signup -->
                <form class="auth-form" id="signup-client" action="auth_signup.php" method="POST">
                    <input type="hidden" name="role" value="client">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">First Name</label><div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="first_name" class="form-input auth-input" placeholder="first name" required></div></div>
                        <div class="form-group"><label class="form-label">Last Name</label><div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="last_name" class="form-input auth-input" placeholder="last name" required></div></div>
                    </div>
                    <div class="form-group"><label class="form-label">Company Name</label><div class="auth-input-wrap"><i class="fas fa-building"></i><input type="text" name="company_name" class="form-input auth-input" placeholder="Your company" required></div></div>
                    <div class="form-group"><label class="form-label">Email Address</label><div class="auth-input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email" class="form-input auth-input" placeholder="email@company.com" required></div></div>
                    <div class="form-group"><label class="form-label">Phone Number</label><div class="auth-input-wrap"><i class="fas fa-phone"></i><input type="tel" name="phone" class="form-input auth-input" placeholder="+966 5XX XXX XXXX" required></div></div>
                    <div class="form-group"><label class="form-label">Password</label><div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" name="password" class="form-input auth-input" id="signup-pass-client" placeholder="Min 8 characters" required><button type="button" class="auth-eye-btn" onclick="togglePassword('signup-pass-client',this)"><i class="fas fa-eye"></i></button></div></div>
                    <label class="auth-terms-check"><input type="checkbox" required> I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
                    <button type="submit" class="btn-auth-submit"><i class="fas fa-rocket"></i> Create Client Account</button>
                </form>
                <!-- Usher Signup -->
                <form class="auth-form" id="signup-usher" style="display:none" action="auth_signup.php" method="POST">
                    <input type="hidden" name="role" value="usher">
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">First Name</label><div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="first_name" class="form-input auth-input" placeholder="first name" required></div></div>
                        <div class="form-group"><label class="form-label">Last Name</label><div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="last_name" class="form-input auth-input" placeholder="last name" required></div></div>
                    </div>
                    <div class="form-group"><label class="form-label">Email Address</label><div class="auth-input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email" class="form-input auth-input" placeholder="email@example.com" required></div></div>
                    <div class="form-group"><label class="form-label">Phone Number</label><div class="auth-input-wrap"><i class="fas fa-phone"></i><input type="tel" name="phone" class="form-input auth-input" placeholder="+966 5XX XXX XXXX" required></div></div>
                    <div class="form-group"><label class="form-label">City</label>
                        <div class="auth-input-wrap"><i class="fas fa-map-marker-alt"></i>
                            <select name="city" class="form-input auth-input"><option>Riyadh</option><option>Jeddah</option><option>Cairo</option><option>Dubai</option><option>Dammam</option></select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">Category</label>
                        <div class="auth-input-wrap"><i class="fas fa-briefcase"></i>
                            <select name="category" class="form-input auth-input" required>
                                <option value="">Select your category...</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Event organizers">Event Organizers</option>
                                <option value="Quality control">Quality Control</option>
                                <option value="Models">Models</option>
                                <option value="Operation">Operation</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">Skills</label><div class="auth-input-wrap"><i class="fas fa-star"></i><input type="text" name="skills" class="form-input auth-input" placeholder="e.g. Customer Service, Bilingual, VIP Handling"></div></div>
                    <div class="form-group"><label class="form-label">Password</label><div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" name="password" class="form-input auth-input" id="signup-pass-usher" placeholder="Min 8 characters" required><button type="button" class="auth-eye-btn" onclick="togglePassword('signup-pass-usher',this)"><i class="fas fa-eye"></i></button></div></div>
                    <label class="auth-terms-check"><input type="checkbox" required> I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
                    <button type="submit" class="btn-auth-submit btn-auth-usher"><i class="fas fa-id-badge"></i> Create Usher Account</button>
                </form>
                <div class="auth-divider"><span>or sign up with</span></div>
                <div class="auth-social-row">
                    <button class="auth-social-btn" type="button" onclick="handleSocialSignup()"><i class="fab fa-google"></i> Google</button>
                    <button class="auth-social-btn" type="button" onclick="handleSocialSignup()"><i class="fab fa-apple"></i> Apple</button>
                </div>
                <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
            </div>
        </div>
    </div>
    <script>
        let selectedRole = 'client';

        // Show error from URL params
        const params = new URLSearchParams(window.location.search);
        const error = params.get('error');
        if (error) {
            const errorDiv = document.getElementById('auth-error');
            const errorText = document.getElementById('error-text');
            const messages = {
                'empty': 'Please fill in all required fields.',
                'exists': 'An account with this email already exists.',
                'failed': 'Registration failed. Please try again.'
            };
            errorText.textContent = messages[error] || 'Signup failed. Please try again.';
            errorDiv.style.display = 'flex';
        }

        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'fas fa-eye-slash'; }
            else { input.type = 'password'; icon.className = 'fas fa-eye'; }
        }
        function switchSignupRole(option) {
            document.querySelectorAll('.toggle-option').forEach(o => o.classList.remove('active'));
            option.classList.add('active');
            selectedRole = option.dataset.role;
            const knob = document.querySelector('.toggle-knob');
            knob.style.transform = selectedRole === 'usher' ? 'translateX(100%)' : 'translateX(0)';
            document.getElementById('signup-client').style.display = selectedRole === 'client' ? '' : 'none';
            document.getElementById('signup-usher').style.display = selectedRole === 'usher' ? '' : 'none';
        }


        function handleSocialSignup() {
            alert('Social signup is not yet available. Please use email/password.');
        }
        function showAuthError(form, msg) {
            let errDiv = form.querySelector('.auth-error');
            if (!errDiv) {
                errDiv = document.createElement('div');
                errDiv.className = 'auth-error';
                errDiv.style.cssText = 'background:#fee;color:#e74c3c;padding:10px 16px;border-radius:8px;font-size:0.88rem;font-weight:600;margin-bottom:12px;border:1px solid #fcc;display:flex;align-items:center;gap:8px;animation:fadeInUp 0.3s ease';
                form.insertBefore(errDiv, form.firstChild);
            }
            errDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            setTimeout(() => errDiv.remove(), 4000);
        }

        // Prevent double submissions and show loading state
        document.querySelectorAll('.auth-form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
            });
        });
    </script>
</body>
</html>
