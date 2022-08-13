import './bootstrap';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', function () {
    // Check for dark mode.
    const darkMode = localStorage.getItem('darkMode');

    if (darkMode === 'true') {
        document.body.classList.add('dark');
    }
}, false);
