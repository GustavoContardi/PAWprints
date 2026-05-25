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

    // --- Historial de búsquedas ---
    const STORAGE_KEY = 'pawprints_search_history';
    const MAX_HISTORY = 5;

    function getHistory() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const parsed = JSON.parse(stored);
                if (Array.isArray(parsed)) {
                    return parsed.filter(item => typeof item === 'string' && item.trim() !== '');
                }
            }
        } catch (e) {
            console.error('Error reading search history from localStorage:', e);
        }
        return [];
    }

    function saveHistory(history) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
        } catch (e) {
            console.error('Error saving search history to localStorage:', e);
        }
    }

    function addToHistory(term) {
        const trimmed = term.trim();
        if (!trimmed) return;

        let history = getHistory();

        // Check duplicates case-insensitively, but keep the new capitalization
        const duplicateIndex = history.findIndex(t => t.toLowerCase() === trimmed.toLowerCase());
        if (duplicateIndex !== -1) {
            history.splice(duplicateIndex, 1);
        }

        history.unshift(trimmed);

        if (history.length > MAX_HISTORY) {
            history = history.slice(0, MAX_HISTORY);
        }

        saveHistory(history);
        renderHistory();
    }

    function renderHistory() {
        const container = document.getElementById('cat-historial');
        if (!container) return;

        const history = getHistory();
        if (history.length === 0) {
            container.innerHTML = '';
            container.style.display = 'none';
            return;
        }

        container.innerHTML = '';
        container.style.display = 'flex';

        const label = document.createElement('span');
        label.className = 'cat-historial-label';
        label.textContent = 'Búsquedas recientes:';
        container.appendChild(label);

        const form = document.querySelector('.cat-busqueda');
        const input = document.getElementById('busqueda');

        history.forEach(term => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'cat-chip cat-historial-btn';
            btn.setAttribute('aria-label', `Buscar "${term}"`);

            // Clock SVG Icon
            btn.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="cat-historial-icon" style="margin-right: 6px; flex-shrink: 0; opacity: 0.7;">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span></span>
            `;
            btn.querySelector('span').textContent = term;

            btn.addEventListener('click', () => {
                if (input && form) {
                    input.value = term;
                    addToHistory(term);
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }
            });

            container.appendChild(btn);
        });
    }

    // Set up form submission handler
    const form = document.querySelector('.cat-busqueda');
    const input = document.getElementById('busqueda');

    if (form && input) {
        form.addEventListener('submit', () => {
            const term = input.value;
            addToHistory(term);
        });
    }

    // Initial render
    renderHistory();
});
