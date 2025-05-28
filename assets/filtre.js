    document.addEventListener('DOMContentLoaded', () => {
    const filtre = document.querySelector('#filtre');
    const mobileMenu = document.getElementById('mobile-menu-filtre');

    filtre.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});
});
