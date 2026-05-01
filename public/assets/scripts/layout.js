document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menu-toggle');
    const menuList = document.getElementById('nav-menu');

    if (menuBtn && menuList) {
        menuBtn.addEventListener('click', (e) => {
            e.preventDefault();
            menuList.classList.toggle('menu-active');
            
            // Opcional: Cambiar aria-expanded
            const isExpanded = menuList.classList.contains('menu-active');
            menuBtn.setAttribute('aria-expanded', isExpanded);
        });
    }
});
