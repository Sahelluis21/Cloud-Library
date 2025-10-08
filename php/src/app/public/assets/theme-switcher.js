document.addEventListener('DOMContentLoaded', function() {
    // Tema
    const themeMenuItem = document.querySelector('.theme-menu-item');
    if (themeMenuItem) {
        themeMenuItem.addEventListener('click', function(e) {
            if (e.target.closest('.theme-submenu')) return;
            this.classList.toggle('active');
        });
    }

    // Suporte
    const supportMenuItem = document.querySelector('.support-menu-item');
    if (supportMenuItem) {
        supportMenuItem.addEventListener('click', function(e) {
            if (e.target.closest('.support-submenu')) return;
            this.classList.toggle('active');
        });
    }

    // Seleção de tema
    const themeOptions = document.querySelectorAll('.theme-submenu li');
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const selectedTheme = this.getAttribute('data-theme');
            document.documentElement.setAttribute('data-theme', selectedTheme);
            localStorage.setItem('theme', selectedTheme);
            themeMenuItem?.classList.remove('active');
            highlightActiveTheme();
        });
    });

    // Destacar tema ativo
    function highlightActiveTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        themeOptions.forEach(option => {
            option.classList.toggle('active', option.getAttribute('data-theme') === currentTheme);
        });
    }

    // Carregar tema salvo
    function loadTheme() {
        const savedTheme = localStorage.getItem('theme') ||
                           (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', savedTheme);
        highlightActiveTheme();
    }

    loadTheme();
});
