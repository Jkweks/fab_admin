// Handle theme selection and persistence
function applyTheme(theme) {
    document.body.setAttribute('data-bs-theme', theme === 'dark' ? 'dark' : 'light');
}

document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('theme-select');
    const current = document.body.getAttribute('data-bs-theme') || 'light';
    const stored = localStorage.getItem('theme');

    const theme = stored || current;
    applyTheme(theme);
    localStorage.setItem('theme', theme);
    if (select) {
        select.value = theme;
        select.addEventListener('change', function (e) {
            const newTheme = e.target.value;
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});
