import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');

    if (loginForm) {
        loginForm.addEventListener('submit', () => {
            loginBtn.innerText = "Authenticating...";
            loginBtn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    }

    // Parallax background movement
    document.addEventListener('mousemove', (e) => {
        const overlay = document.querySelector('.background-overlay');
        if (overlay) {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
            overlay.style.transform = `translate(${moveX}px, ${moveY}px) scale(1.05)`;
        }
    });
});