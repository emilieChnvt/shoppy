document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('#menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const icons = menuButton.querySelectorAll('svg');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        icons.forEach(icon => icon.classList.toggle('hidden'));
    });
});