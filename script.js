// ===== PROJECT DATA =====
const projects = [
    {
        title: "Insomnia Egypt Gaming Festival 2026",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 3,
        image: "images/event_gaming.png",
        type: "gaming"
    },
    {
        title: "Insomnia Egypt Gaming Festival 2026",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 3,
        image: "images/event_training.png",
        type: "gaming"
    },
    {
        title: "Insomnia Egypt Gaming Festival 2026",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 3,
        image: "images/event_training.png",
        type: "gaming"
    },
    {
        title: "Insomnia Egypt Gaming Festival 2026",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 3,
        image: "images/event_gaming.png",
        type: "gaming"
    },
    {
        title: "Foundation training year 2025/26: the skills you need to help you",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 3,
        image: "images/event_training.png",
        type: "training"
    },
    {
        title: "Foundation training year 2025/26: the skills you need to help you",
        date: "Nov 02, 2024",
        location: "The Ritz-Carlton, Riyadh",
        ushers: 7,
        image: "images/event_training.png",
        type: "training"
    }
];

// ===== DOM READY =====
document.addEventListener('DOMContentLoaded', () => {
    renderProjects();
    animateOnScroll();
    countUpAnimation();
    setupNavLinks();
    setupPagination();
    setupSearch();
});

// ===== RENDER PROJECT CARDS =====
function renderProjects() {
    const grid = document.getElementById('projects-grid');
    grid.innerHTML = '';

    projects.forEach((project, index) => {
        const card = document.createElement('div');
        card.className = 'project-card';
        card.style.animationDelay = `${index * 0.1}s`;

        card.innerHTML = `
            <div class="card-image">
                <img src="${project.image}" alt="${project.title}" loading="lazy">
            </div>
            <div class="card-body">
                <h3 class="card-title">${project.title}</h3>
                <div class="card-detail">
                    <i class="far fa-calendar"></i>
                    <span>${project.date}</span>
                </div>
                <div class="card-detail">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${project.location}</span>
                </div>
                <div class="card-detail">
                    <i class="fas fa-users"></i>
                    <span>${project.ushers} ushers remaining</span>
                </div>
                <button class="btn-manage" onclick="handleManageProject(${index})">Manage Project</button>
            </div>
        `;

        grid.appendChild(card);
    });

    // Trigger staggered animation
    requestAnimationFrame(() => {
        const cards = grid.querySelectorAll('.project-card');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, i * 100);
        });
    });
}

// ===== COUNT-UP ANIMATION =====
function countUpAnimation() {
    const statValues = document.querySelectorAll('.stat-value');
    
    statValues.forEach((el, index) => {
        const target = parseInt(el.getAttribute('data-target'));
        const duration = 1500;
        const startTime = performance.now();
        const delay = index * 200;

        setTimeout(() => {
            el.style.animation = 'countUp 0.4s ease-out';

            function update(currentTime) {
                const elapsed = currentTime - startTime - delay;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(eased * target);
                el.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    el.textContent = target;
                }
            }

            requestAnimationFrame(update);
        }, delay);
    });

    // Animate stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, i * 150);
    });
}

// ===== SCROLL ANIMATIONS =====
function animateOnScroll() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1, rootMargin: '0px 0px -50px 0px' }
    );

    // Observe sections and animate-on-scroll elements
    const sections = document.querySelectorAll('.filters-row, .projects-section, .footer, .animate-on-scroll, .feature-card, .step-card, .testimonial-card, .dashboard-panel, .settings-card, .help-section, .gig-card');
    sections.forEach(section => observer.observe(section));
}

// ===== NAV LINKS =====
function setupNavLinks() {
    // Top nav - add ripple effect (links navigate naturally now)
    const navLinks = document.querySelectorAll('.nav-links a');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            addRipple(link, e);
        });
    });
}

// ===== PAGINATION =====
function setupPagination() {
    const pageButtons = document.querySelectorAll('.page-btn[data-page]');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    let currentPage = 1;

    function setPage(page) {
        currentPage = page;
        pageButtons.forEach(btn => {
            btn.classList.toggle('active', parseInt(btn.dataset.page) === page);
        });

        // Re-animate cards on page change
        const grid = document.getElementById('projects-grid');
        const cards = grid.querySelectorAll('.project-card');
        cards.forEach((card, i) => {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.95) translateY(15px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                card.style.opacity = '1';
                card.style.transform = 'scale(1) translateY(0)';
            }, i * 80);
        });
    }

    pageButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            setPage(parseInt(btn.dataset.page));
        });
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) setPage(currentPage - 1);
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage < 3) setPage(currentPage + 1);
    });
}

// ===== SEARCH =====
function setupSearch() {
    const searchInput = document.getElementById('search-input');
    let debounceTimer;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.project-card');

            cards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const match = title.includes(query);
                
                if (match) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    requestAnimationFrame(() => {
                        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    });
                } else {
                    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        }, 250);
    });
}

// ===== MANAGE PROJECT HANDLER =====
function handleManageProject(index) {
    window.location.href = 'projects.html';
}

// ===== RIPPLE EFFECT =====
function addRipple(element, e) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        width: ${size}px;
        height: ${size}px;
        left: ${e.clientX - rect.left - size/2}px;
        top: ${e.clientY - rect.top - size/2}px;
        transform: scale(0);
        animation: rippleEffect 0.6s ease-out;
        pointer-events: none;
    `;

    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
}

// Add ripple keyframes dynamically
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes rippleEffect {
        to { transform: scale(2.5); opacity: 0; }
    }
`;
document.head.appendChild(rippleStyle);
