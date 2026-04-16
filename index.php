<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasla - Connect Events with Talent</title>
    <meta name="description" content="Wasla is the leading platform connecting event managers with professional ushers and coordinators across the MENA region.">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="images/wasla-icon.png">
</head>
<body class="landing-body">
    <!-- Landing Navbar -->
    <nav class="landing-nav">
        <a href="index.php" class="logo">
            <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="36" height="36">
            <span class="logo-text">Wasla</span>
        </a>
        <div class="landing-nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#testimonials">Testimonials</a>
            <a href="contact.php">Contact</a>
        </div>
        <div class="landing-nav-actions">
            <a href="login.php" class="btn-landing-login">Log In</a>
            <a href="signup.php" class="btn-landing-signup">Get Started</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg-shapes">
            <div class="hero-shape hero-shape-1"></div>
            <div class="hero-shape hero-shape-2"></div>
            <div class="hero-shape hero-shape-3"></div>
        </div>
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-bolt"></i> The #1 Event Staffing Platform</div>
            <h1 class="hero-title">Connect Your Events<br>With <span class="hero-gradient-text">Top Talent</span></h1>
            <p class="hero-subtitle">Wasla bridges the gap between event managers and professional ushers. Find, hire, and manage event staff seamlessly — all in one powerful platform.</p>
            <div class="hero-actions">
                <a href="signup.php" class="btn-hero-primary"><i class="fas fa-rocket"></i> Start For Free</a>
                <a href="#how-it-works" class="btn-hero-secondary"><i class="fas fa-play-circle"></i> See How It Works</a>
            </div>
            <div class="hero-stats-row">
                <div class="hero-stat"><span class="hero-stat-number">500+</span><span class="hero-stat-label">Events Managed</span></div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat"><span class="hero-stat-number">2,000+</span><span class="hero-stat-label">Professional Ushers</span></div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat"><span class="hero-stat-number">98%</span><span class="hero-stat-label">Satisfaction Rate</span></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="landing-section" id="features">
        <div class="landing-container">
            <div class="section-header">
                <span class="section-badge">Features</span>
                <h2 class="landing-section-title">Everything You Need to Run<br>World-Class Events</h2>
                <p class="landing-section-desc">From project setup to team coordination, Wasla provides a complete suite of tools.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card animate-on-scroll stagger-1">
                    <div class="feature-icon"><i class="fas fa-project-diagram"></i></div>
                    <h3>Project Management</h3>
                    <p>Create, organize, and track your events with an intuitive dashboard that keeps everything on schedule.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon-green"><i class="fas fa-user-check"></i></div>
                    <h3>Smart Matching</h3>
                    <p>Our algorithm matches your events with the perfect ushers based on skills, location, and availability.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon-purple"><i class="fas fa-comments"></i></div>
                    <h3>Real-Time Messaging</h3>
                    <p>Communicate instantly with your team through our built-in messaging system — no third-party apps needed.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon-orange"><i class="fas fa-chart-line"></i></div>
                    <h3>Analytics & Reports</h3>
                    <p>Track performance metrics, hiring trends, and event outcomes with detailed visual reports.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon-red"><i class="fas fa-shield-alt"></i></div>
                    <h3>Verified Profiles</h3>
                    <p>Every usher is background-checked and rated by previous event managers for complete peace of mind.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon feature-icon-teal"><i class="fas fa-globe"></i></div>
                    <h3>MENA Coverage</h3>
                    <p>Access talent across Saudi Arabia, Egypt, UAE, and the wider MENA region from a single platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="landing-section landing-section-dark" id="how-it-works">
        <div class="landing-container">
            <div class="section-header">
                <span class="section-badge section-badge-light">How It Works</span>
                <h2 class="landing-section-title" style="color:#fff">Get Started in Three Simple Steps</h2>
            </div>
            <div class="steps-row">
                <div class="step-card">
                    <div class="step-number-large">1</div>
                    <h3>Create Your Project</h3>
                    <p>Set up your event with details like date, location, and the number of ushers you need.</p>
                </div>
                <div class="step-connector"><i class="fas fa-arrow-right"></i></div>
                <div class="step-card">
                    <div class="step-number-large">2</div>
                    <h3>Review Applicants</h3>
                    <p>Browse verified usher profiles, check ratings, and select the best candidates for your event.</p>
                </div>
                <div class="step-connector"><i class="fas fa-arrow-right"></i></div>
                <div class="step-card">
                    <div class="step-number-large">3</div>
                    <h3>Manage & Execute</h3>
                    <p>Coordinate your team in real-time, track attendance, and deliver a flawless event experience.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="landing-section" id="testimonials">
        <div class="landing-container">
            <div class="section-header">
                <span class="section-badge">Testimonials</span>
                <h2 class="landing-section-title">Loved by Event Professionals</h2>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="testimonial-text">"Wasla transformed how we staff our gaming festivals. We found 15 amazing ushers in just 2 days — the quality was outstanding."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><i class="fas fa-user-circle"></i></div>
                        <div><strong>Omar Hassan</strong><span>Event Director, Insomnia Egypt</span></div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="testimonial-text">"The messaging system is a game-changer. We coordinated a team of 30 ushers seamlessly through Wasla's platform."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar testimonial-avatar-purple"><i class="fas fa-user-circle"></i></div>
                        <div><strong>Sara Ahmed</strong><span>Operations Manager, MDLBEAST</span></div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                    <p class="testimonial-text">"Professional, reliable, and incredibly easy to use. Wasla is now our go-to for every corporate event we organize."</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar testimonial-avatar-green"><i class="fas fa-user-circle"></i></div>
                        <div><strong>Khalid Al-Farsi</strong><span>CEO, Gulf Events Co.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="landing-container">
            <h2 class="cta-title">Ready to Elevate Your Events?</h2>
            <p class="cta-desc">Join thousands of event managers who trust Wasla to deliver exceptional results.</p>
            <a href="signup.php" class="btn-hero-primary btn-cta"><i class="fas fa-arrow-right"></i> Get Started Today</a>
        </div>
    </section>

    <!-- Landing Footer -->
    <footer class="landing-footer">
        <div class="landing-container">
            <div class="landing-footer-grid">
                <div class="landing-footer-brand">
                    <div class="logo" style="margin-bottom:12px">
                        <img src="images/wasla-icon.png" alt="Wasla" class="logo-icon" width="32" height="32">
                        <span class="logo-text" style="color:#fff">Wasla</span>
                    </div>
                    <p class="landing-footer-desc">The leading platform connecting event managers with professional talent across the MENA region.</p>
                    <div class="landing-social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <div class="landing-footer-col">
                    <h4>Platform</h4>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="projects.php">My Projects</a>
                    <a href="create-project.php">Create Project</a>
                    <a href="messages.php">Messages</a>
                </div>
                <div class="landing-footer-col">
                    <h4>Company</h4>
                    <a href="contact.php">Contact Us</a>
                    <a href="help.php">Help Center</a>
                    <a href="terms.php">Terms of Service</a>
                    <a href="privacy.php">Privacy Policy</a>
                </div>
                <div class="landing-footer-col">
                    <h4>Connect</h4>
                    <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                    <a href="#"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                </div>
            </div>
            <div class="landing-footer-bottom">
                <p>&copy; 2024 Wasla Digital Conduit. All rights reserved.</p>
                <div class="landing-footer-bottom-links">
                    <a href="terms.php">Terms</a>
                    <a href="privacy.php">Privacy</a>
                    <a href="contact.php">Contact</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Scroll Reveal Animation
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

            document.querySelectorAll('.animate-on-scroll, .feature-card, .step-card, .testimonial-card').forEach(el => {
                observer.observe(el);
            });

            // Counter animation for hero stats
            const statNums = document.querySelectorAll('.hero-stat-number');
            statNums.forEach(el => {
                const text = el.textContent;
                const num = parseInt(text.replace(/[^0-9]/g, ''));
                const suffix = text.replace(/[0-9,]/g, '');
                let current = 0;
                const step = Math.ceil(num / 60);
                const timer = setInterval(() => {
                    current += step;
                    if (current >= num) {
                        el.textContent = text;
                        clearInterval(timer);
                    } else {
                        el.textContent = current.toLocaleString() + suffix;
                    }
                }, 25);
            });
        });
    </script>
</body>
</html>
