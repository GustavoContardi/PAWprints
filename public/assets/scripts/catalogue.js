document.addEventListener('DOMContentLoaded', () => {
    const filterBtn = document.querySelector('.cat-btn-filtro');
    const sidebar = document.querySelector('.cat-sidebar');

    if (filterBtn && sidebar) {
        filterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('sidebar-active');
            
            const isActive = sidebar.classList.contains('sidebar-active');
            filterBtn.setAttribute('aria-expanded', isActive);
        });
    }
});
