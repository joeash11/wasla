// ===== Wasla Global Theme & Language Initializer =====
// Include this script on every page to persist theme and language settings
(function() {
    // Apply saved theme
    const theme = localStorage.getItem('wasla_theme') || 'light';
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else if (theme === 'system') {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
        // Listen for system changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem('wasla_theme') === 'system') {
                if (e.matches) {
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                }
            }
        });
    }

    // Apply saved language direction
    const lang = localStorage.getItem('wasla_language') || 'en';
    if (lang === 'ar') {
        document.documentElement.dir = 'rtl';
    }
})();
