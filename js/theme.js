// theme.js — Dark/Light Mode Toggle with localStorage persistence

(function () {
    var STORAGE_KEY = 'canteen_theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
    }

   
    var saved = localStorage.getItem(STORAGE_KEY) || 'light';
    applyTheme(saved);

    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('themeToggle');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var current = document.documentElement.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem(STORAGE_KEY, next);
            btn.style.transform = 'rotate(360deg) scale(1.2)';
            setTimeout(function() { btn.style.transform = ''; }, 400);
        });
    });
})();
