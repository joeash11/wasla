// ===== Wasla Global Theme & Language Initializer =====
// Include this script on every page to persist theme and language settings.
// Translations are fetched from /api/translations.php?lang=XX
(function() {
    // Capture the base URL from this script's own src (works from any subdirectory)
    const _self = document.currentScript;
    const _scriptSrc = _self ? _self.src : '';
    // e.g. http://localhost/grad%20proj/wasla-theme.js  →  http://localhost/grad%20proj/
    const BASE_URL = _scriptSrc ? _scriptSrc.replace(/wasla-theme\.js.*$/, '') : '/';

    // ── 1. Apply saved theme ──────────────────────────────────────────────────
    const theme = localStorage.getItem('wasla_theme') || 'light';
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else if (theme === 'system') {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem('wasla_theme') === 'system') {
                document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : '');
                if (!e.matches) document.documentElement.removeAttribute('data-theme');
            }
        });
    }

    // ── 2. Apply RTL immediately for Arabic (before paint) ───────────────────
    const lang = localStorage.getItem('wasla_language') || 'en';
    if (lang === 'ar') {
        document.documentElement.dir = 'rtl';
        document.documentElement.classList.add('lang-ar');
    } else {
        document.documentElement.dir = 'ltr';
        document.documentElement.classList.remove('lang-ar');
    }

    // ── Brand names that must NEVER be translated under any circumstances ──
    const NEVER_TRANSLATE = ['Wasla', 'wasla', 'WASLA'];

    // ── 3. Translation engine ─────────────────────────────────────────────────
    function translateNode(node, dict) {
        if (node.nodeType === 3) {
            const text = node.nodeValue.trim();
            // Hard-protect brand names — never replace these
            if (NEVER_TRANSLATE.some(w => text === w)) return;
            if (text && dict[text]) {
                node.nodeValue = node.nodeValue.replace(text, dict[text]);
            }
        } else if (node.nodeType === 1 && node.nodeName !== 'SCRIPT' && node.nodeName !== 'STYLE') {
            // Skip elements explicitly marked as non-translatable
            if (node.getAttribute && node.getAttribute('translate') === 'no') return;
            if (node.placeholder && dict[node.placeholder]) {
                node.placeholder = dict[node.placeholder];
            }
            if (node.title && dict[node.title]) {
                node.title = dict[node.title];
            }
            if (node.tagName === 'INPUT' && (node.type === 'submit' || node.type === 'button')) {
                const val = (node.value || '').trim();
                if (val && dict[val]) node.value = dict[val];
            }
            for (let i = 0; i < node.childNodes.length; i++) {
                translateNode(node.childNodes[i], dict);
            }
        }
    }

    function applyTranslations(dict) {
        if (!dict || Object.keys(dict).length === 0) return;
        window.wasla_i18n = dict;
        translateNode(document.body, dict);

        // Watch for dynamically added/changed DOM (modals, API-rendered content, etc.)
        if (window.MutationObserver) {
            let debounceTimer = null;
            const observer = new MutationObserver((mutations) => {
                // Debounce to avoid translating the same nodes multiple times
                if (debounceTimer) clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    mutations.forEach(m => {
                        // New nodes added (e.g. innerHTML replacement)
                        m.addedNodes.forEach(node => {
                            if (node.nodeType === 1) translateNode(node, dict);
                        });
                        // Text changed on existing node (e.g. .textContent = ...)
                        if (m.type === 'characterData' && m.target.nodeType === 3) {
                            const text = m.target.nodeValue.trim();
                            if (text && dict[text]) {
                                m.target.nodeValue = m.target.nodeValue.replace(text, dict[text]);
                            }
                        }
                    });
                }, 50);
            });
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                characterData: true
            });
        }
    }


    function loadTranslations(langCode) {
        if (langCode === 'en') return Promise.resolve({});

        // Cache in sessionStorage so we don't re-fetch every page
        // Bump TRANS_VER whenever ar.json is updated to bust stale cache
        const TRANS_VER = 6;
        const cacheKey = 'wasla_trans_' + langCode + '_v' + TRANS_VER;
        const cached = sessionStorage.getItem(cacheKey);
        if (cached) {
            try { return Promise.resolve(JSON.parse(cached)); } catch (e) {}
        }

        // Build API URL using the script's own base (works from any page depth)
        const apiUrl = BASE_URL + 'api/translations.php?lang=' + langCode;

        return fetch(apiUrl)
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data && !data.error) {
                    sessionStorage.setItem(cacheKey, JSON.stringify(data));
                }
                return data || {};
            })
            .catch(() => ({}));
    }

    // ── 4. DOMContentLoaded ───────────────────────────────────────────────────
    window.addEventListener('DOMContentLoaded', () => {

        // ── Quick Start Guide FAB ─────────────────────────────────────────────
        const path = window.location.pathname;
        if (!path.includes('login') && !path.includes('signup') && !path.includes('admin')) {
            const fab = document.createElement('button');
            fab.innerHTML = '<i class="fas fa-rocket"></i>';
            fab.className = 'global-guide-fab pulse';
            fab.setAttribute('title', 'Quick Start Guide');
            document.body.appendChild(fab);

            const isUsher = path.includes('/usher/');
            let modalBodyHTML = isUsher ? `
                <div class="guide-step"><div class="guide-step-num">1</div><div>
                    <h4>Find Jobs</h4>
                    <p>Find active projects from clients across different categories that suit you.</p>
                </div></div>
                <div class="guide-step"><div class="guide-step-num">2</div><div>
                    <h4>Apply</h4>
                    <p>Select a job, review the details, and send your application easily.</p>
                </div></div>
                <div class="guide-step"><div class="guide-step-num">3</div><div>
                    <h4>Communicate</h4>
                    <p>Message the client directly regarding questions and project details.</p>
                </div></div>
            ` : `
                <div class="guide-step"><div class="guide-step-num">1</div><div>
                    <h4>Create a Project</h4>
                    <p>Head over to <strong>My Projects</strong> and click "Create Project" to set up your event.</p>
                </div></div>
                <div class="guide-step"><div class="guide-step-num">2</div><div>
                    <h4>Review Applications</h4>
                    <p>Once live, ushers will apply. Review their profiles and ratings before accepting.</p>
                </div></div>
                <div class="guide-step"><div class="guide-step-num">3</div><div>
                    <h4>Communicate</h4>
                    <p>Use the <strong>Messages</strong> tab to communicate with approved ushers in real-time.</p>
                </div></div>
            `;

            const modal = document.createElement('div');
            modal.className = 'guide-modal-overlay';
            modal.innerHTML = `
                <div class="guide-modal-content">
                    <button class="guide-modal-close"><i class="fas fa-times"></i></button>
                    <div class="guide-modal-header">
                        <div class="guide-modal-icon"><i class="fas fa-rocket"></i></div>
                        <h2 class="guide-modal-title">Wasla Quick Start Guide</h2>
                        <p class="guide-modal-subtitle">Learn the basics of Wasla in 5 minutes</p>
                    </div>
                    <div class="guide-modal-body">${modalBodyHTML}</div>
                    <button class="btn-guide-primary" id="btn-guide-gotit">Got it, thanks!</button>
                </div>
            `;
            document.body.appendChild(modal);

            const style = document.createElement('style');
            style.textContent = `
                .global-guide-fab{position:fixed;bottom:30px;right:30px;width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--cyan-dark),var(--cyan));color:#fff;border:none;font-size:1.5rem;cursor:pointer;box-shadow:0 4px 15px rgba(3,152,197,0.4);z-index:9999;display:flex;align-items:center;justify-content:center;transition:transform 0.3s ease}
                .global-guide-fab:hover{transform:scale(1.1) translateY(-5px)}
                .guide-modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:10000;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px);opacity:0;transition:opacity 0.3s ease}
                .guide-modal-overlay.active{display:flex;opacity:1}
                .guide-modal-content{background:var(--white);width:90%;max-width:500px;border-radius:16px;padding:32px;position:relative;transform:scale(0.9);transition:transform 0.3s cubic-bezier(0.175,0.885,0.32,1.275);box-shadow:0 10px 40px rgba(0,0,0,0.2)}
                .guide-modal-overlay.active .guide-modal-content{transform:scale(1)}
                .guide-modal-close{position:absolute;top:20px;right:20px;background:none;border:none;font-size:1.2rem;color:var(--gray-400);cursor:pointer;transition:color 0.2s}
                .guide-modal-close:hover{color:#e74c3c}
                .guide-modal-header{text-align:center;margin-bottom:24px}
                .guide-modal-icon{width:64px;height:64px;background:rgba(79,195,247,0.1);color:var(--cyan-dark);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 16px}
                .guide-modal-title{font-size:1.5rem;font-weight:800;color:var(--primary);margin-bottom:8px}
                .guide-modal-subtitle{color:var(--gray-600);font-size:0.95rem}
                .guide-modal-body{margin-bottom:24px}
                .guide-step{display:flex;gap:16px;margin-bottom:20px}
                .guide-step-num{width:32px;height:32px;min-width:32px;border-radius:50%;background:var(--gray-100);color:var(--cyan-dark);display:flex;align-items:center;justify-content:center;font-weight:700;border:1px solid var(--gray-200)}
                .guide-step h4{font-size:1rem;color:var(--primary);margin-bottom:4px;font-weight:700}
                .guide-step p{font-size:0.88rem;color:var(--gray-600);line-height:1.4}
                .btn-guide-primary{width:100%;padding:14px;border:none;background:var(--accent);color:var(--primary);font-weight:700;font-size:1rem;border-radius:8px;cursor:pointer;transition:background 0.3s}
                .btn-guide-primary:hover{background:var(--accent-hover)}
            `;
            document.head.appendChild(style);

            function openGuide() { modal.style.display = 'flex'; setTimeout(() => modal.classList.add('active'), 10); }
            function closeGuide() { modal.classList.remove('active'); setTimeout(() => modal.style.display = 'none', 300); }

            fab.addEventListener('click', openGuide);
            document.querySelector('.guide-modal-close').addEventListener('click', closeGuide);
            document.getElementById('btn-guide-gotit').addEventListener('click', closeGuide);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeGuide(); });
            document.querySelectorAll('.trigger-quick-start').forEach(el => {
                el.addEventListener('click', (e) => { e.preventDefault(); openGuide(); });
            });
        }

        // ── Load & apply translations from API ────────────────────────────────
        if (lang !== 'en') {
            loadTranslations(lang).then(dict => applyTranslations(dict));
        }
    });

    // ── 5. Global helpers ─────────────────────────────────────────────────────
    // Call window.waslaSetLanguage('ar') or ('en') from any settings page
    window.waslaSetLanguage = function(newLang) {
        localStorage.setItem('wasla_language', newLang);
        sessionStorage.removeItem('wasla_trans_' + newLang);
        location.reload();
    };

})();
