/**
 * PAWprints — catalogue.js
 * Client-side Catalog Management (Filtering, Sorting, Pagination, Recent Searches)
 * 100% JS logic with high-fidelity mobile and desktop experiences.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ── 1. DOM Elements ──────────────────────────────────────────────────────
    const filterBtn = document.querySelector('.cat-btn-filtro');
    const sidebar = document.querySelector('.cat-sidebar');
    const closeBtn = document.querySelector('.cat-sidebar-close');
    const filterForm = document.querySelector('.cat-filtros');
    const searchForm = document.querySelector('.cat-busqueda');
    const searchInput = document.getElementById('busqueda');
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    const perPageSelect = document.getElementById('per_page');
    const paginacionModeSelect = document.getElementById('paginacion_mode');
    
    // ── 2. State Variables ───────────────────────────────────────────────────
    let allBooks = [];
    let filteredBooks = [];
    let currentPage = 1;
    let perPage = 12;
    let searchQuery = '';
    let minPrice = null;
    let maxPrice = null;
    let selectedCategories = [];
    let selectedAges = [];
    let sortKey = 'new'; // 'new', 'popular', 'price'
    let sortOrder = 'desc'; // 'asc' or 'desc'
    let paginationMode = 'traditional'; // 'traditional' or 'infinite'
    
    // Infinite scroll loading variables
    let loadedInfiniteCount = 12;
    let loadingMore = false;
    
    // Recent searches list
    let recentSearches = [];

    // ── 3. Parse Initial Data ───────────────────────────────────────────────
    const booksDataElement = document.getElementById('books-json');
    if (booksDataElement) {
        try {
            allBooks = JSON.parse(booksDataElement.textContent);
            // Normalize types
            allBooks.forEach(b => {
                b.price = parseFloat(b.price) || 0.0;
                b.sales = parseInt(b.sales) || 0;
                b.is_new = b.is_new === true || b.is_new === 'true' || b.is_new === 1 || b.is_new === '1';
            });
        } catch (err) {
            console.error("Error parsing books data:", err);
        }
    }

    // ── 4. Mobile Sidebar UI Actions ─────────────────────────────────────────
    if (filterBtn && sidebar) {
        filterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.add('sidebar-active');
            filterBtn.setAttribute('aria-expanded', 'true');
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeSidebar();
        });
    }

    function closeSidebar() {
        if (sidebar && sidebar.classList.contains('sidebar-active')) {
            sidebar.classList.remove('sidebar-active');
            if (filterBtn) {
                filterBtn.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // Close mobile sidebar on resize if desktop size reached
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 900) {
            closeSidebar();
        }
    });

    // ── 5. Recent Searches Logic (Consigna 4) ──────────────────────────────
    function loadRecentSearches() {
        try {
            const stored = localStorage.getItem('recent_searches');
            recentSearches = stored ? JSON.parse(stored) : [];
        } catch (e) {
            recentSearches = [];
        }
        renderRecentSearches();
    }

    function saveRecentSearch(query) {
        if (!query || !query.trim()) return;
        query = query.trim();

        // Avoid duplicates and bump to the top
        recentSearches = recentSearches.filter(s => s.toLowerCase() !== query.toLowerCase());
        recentSearches.unshift(query);

        // Keep last 5 searches
        if (recentSearches.length > 5) {
            recentSearches.pop();
        }

        localStorage.setItem('recent_searches', JSON.stringify(recentSearches));
        renderRecentSearches();
    }

    function removeRecentSearch(query) {
        recentSearches = recentSearches.filter(s => s !== query);
        localStorage.setItem('recent_searches', JSON.stringify(recentSearches));
        renderRecentSearches();
    }

    function renderRecentSearches() {
        const container = document.getElementById('recent-searches-container');
        const list = document.getElementById('recent-searches-list');
        if (!container || !list) return;

        if (recentSearches.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';
        list.innerHTML = '';
        recentSearches.forEach(search => {
            const li = document.createElement('li');
            li.className = 'cat-keyword';
            li.style.cursor = 'pointer';

            const textSpan = document.createElement('span');
            textSpan.textContent = search;
            textSpan.style.flexGrow = '1';
            textSpan.addEventListener('click', () => {
                searchQuery = search;
                if (searchInput) searchInput.value = search;
                saveRecentSearch(search);
                currentPage = 1;
                loadedInfiniteCount = perPage;
                updateCatalogue();
                closeSidebar();
            });

            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.innerHTML = '&times;';
            delBtn.ariaLabel = `Eliminar búsqueda "${search}"`;
            delBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeRecentSearch(search);
            });

            li.appendChild(textSpan);
            li.appendChild(delBtn);
            list.appendChild(li);
        });
    }

    // ── 6. Filter & Sorting Core Logic (Consigna 3) ──────────────────────────
    function applyFilters() {
        filteredBooks = allBooks.filter(book => {
            // Search query text check (title or author)
            if (searchQuery) {
                const query = searchQuery.toLowerCase().trim();
                const matchTitle = book.title.toLowerCase().includes(query);
                const matchAuthor = book.author.toLowerCase().includes(query);
                if (!matchTitle && !matchAuthor) return false;
            }

            // Min Price
            if (minPrice !== null && book.price < minPrice) return false;

            // Max Price
            if (maxPrice !== null && book.price > maxPrice) return false;

            // Category checks
            if (selectedCategories.length > 0 && !selectedCategories.includes(book.category)) return false;

            // Age checks
            if (selectedAges.length > 0 && !selectedAges.includes(book.age)) return false;

            return true;
        });

        applySorting();
    }

    function applySorting() {
        filteredBooks.sort((a, b) => {
            let valA, valB;
            if (sortKey === 'new') {
                valA = a.is_new ? 1 : 0;
                valB = b.is_new ? 1 : 0;
                if (valA !== valB) {
                    return sortOrder === 'desc' ? valB - valA : valA - valB;
                }
                // Fallback to secondary order ID
                return sortOrder === 'desc' ? b.id - a.id : a.id - b.id;
            } else if (sortKey === 'popular') {
                valA = a.sales;
                valB = b.sales;
            } else if (sortKey === 'price') {
                valA = a.price;
                valB = b.price;
            } else {
                valA = a.id;
                valB = b.id;
            }

            if (valA < valB) return sortOrder === 'asc' ? -1 : 1;
            if (valA > valB) return sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // ── 7. URL State Synchronization ─────────────────────────────────────────
    function syncStateFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('search')) searchQuery = urlParams.get('search');
        if (urlParams.has('min_price')) minPrice = parseFloat(urlParams.get('min_price')) || null;
        if (urlParams.has('max_price')) maxPrice = parseFloat(urlParams.get('max_price')) || null;
        
        if (urlParams.has('category[]')) {
            selectedCategories = urlParams.getAll('category[]');
        } else if (urlParams.has('category')) {
            selectedCategories = urlParams.getAll('category');
        }

        if (urlParams.has('age[]')) {
            selectedAges = urlParams.getAll('age[]');
        } else if (urlParams.has('age')) {
            selectedAges = urlParams.getAll('age');
        }

        if (urlParams.has('order')) sortKey = urlParams.get('order');
        if (urlParams.has('page')) currentPage = parseInt(urlParams.get('page')) || 1;
        if (urlParams.has('per_page')) perPage = parseInt(urlParams.get('per_page')) || 12;
        if (urlParams.has('pagination_mode')) paginationMode = urlParams.get('pagination_mode') || 'traditional';

        // Set default orders based on keys
        if (urlParams.has('sort_order')) {
            sortOrder = urlParams.get('sort_order');
        } else {
            sortOrder = sortKey === 'price' ? 'asc' : 'desc';
        }

        loadedInfiniteCount = perPage;

        // Apply input values to DOM elements
        if (searchInput) searchInput.value = searchQuery;
        if (minPriceInput) minPriceInput.value = minPrice !== null ? minPrice : '';
        if (maxPriceInput) maxPriceInput.value = maxPrice !== null ? maxPrice : '';
        if (perPageSelect) perPageSelect.value = perPage;
        if (paginacionModeSelect) paginacionModeSelect.value = paginationMode;

        // Check checkboxes
        document.querySelectorAll('input[name="category[]"]').forEach(cb => {
            cb.checked = selectedCategories.includes(cb.value);
        });

        document.querySelectorAll('input[name="age[]"]').forEach(cb => {
            cb.checked = selectedAges.includes(cb.value);
        });
    }

    function updateURL() {
        const params = new URLSearchParams();
        if (searchQuery) params.set('search', searchQuery);
        if (minPrice !== null) params.set('min_price', minPrice);
        if (maxPrice !== null) params.set('max_price', maxPrice);
        selectedCategories.forEach(cat => params.append('category[]', cat));
        selectedAges.forEach(age => params.append('age[]', age));
        params.set('order', sortKey);
        params.set('sort_order', sortOrder);
        params.set('page', currentPage);
        params.set('per_page', perPage);
        params.set('pagination_mode', paginationMode);

        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.replaceState(null, '', newUrl);

        // Update CSV Export link dynamically!
        const csvBtn = document.querySelector('.cat-btn-exportar');
        if (csvBtn) {
            csvBtn.href = `/catalogue/export?${params.toString()}`;
        }
    }

    // ── 8. DOM Rendering & Components ────────────────────────────────────────
    function renderGrid() {
        const grid = document.querySelector('.cat-grid');
        if (!grid) return;

        grid.innerHTML = '';

        let booksToRender = [];
        if (paginationMode === 'traditional') {
            const startIdx = (currentPage - 1) * perPage;
            const endIdx = Math.min(startIdx + perPage, filteredBooks.length);
            booksToRender = filteredBooks.slice(startIdx, endIdx);
        } else {
            // Infinite Scroll
            booksToRender = filteredBooks.slice(0, loadedInfiniteCount);
        }

        if (booksToRender.length === 0) {
            grid.innerHTML = `
                <div class="cat-empty-state">
                    <div class="cat-empty-state-icon"></div>
                    <h3>Sin resultados</h3>
                    <p>No se encontraron libros que coincidan con tu búsqueda o los filtros seleccionados.</p>
                </div>
            `;
            return;
        }

        booksToRender.forEach(book => {
            const card = document.createElement('article');
            card.className = 'cat-card';

            // Format price: $15.000,00
            const formattedPrice = new Intl.NumberFormat('es-AR', {
                style: 'currency',
                currency: 'ARS',
                minimumFractionDigits: 2
            }).format(book.price).replace('ARS', '$').trim();

            card.innerHTML = `
                <a href="/book/${book.id}">
                    <img src="/assets/img/${escapeHtml(book.image || 'placeholder.jpg')}" alt="Portada del libro">
                </a>
                <h3>${escapeHtml(book.title)}</h3>
                <p class="cat-card-autor">${escapeHtml(book.author)}</p>
                <p class="cat-card-precio">${formattedPrice}</p>
                <a href="/reserve/${book.id}" class="cat-btn-carrito" aria-label="Reservar libro"></a>
            `;
            grid.appendChild(card);
        });

        // Add infinite scroll trigger loading item if needed
        if (paginationMode === 'infinite' && loadedInfiniteCount < filteredBooks.length) {
            const trigger = document.createElement('div');
            trigger.id = 'infinite-scroll-trigger';
            trigger.style.gridColumn = '1 / -1';
            trigger.style.textAlign = 'center';
            trigger.style.padding = '2rem 1rem';
            trigger.style.color = 'var(--color-brand)';
            trigger.style.fontWeight = '600';
            trigger.style.fontSize = '0.9375rem';
            trigger.innerHTML = `
                <div class="spinner-icon" style="display:inline-block; width: 20px; height: 20px; border: 2.5px solid rgba(92,6,140,0.1); border-top-color: var(--color-brand); border-radius: 50%; animation: spin 0.8s linear infinite; margin-right: 0.5rem; vertical-align: middle;"></div>
                Cargando más libros...
            `;
            grid.appendChild(trigger);
        }
    }

    function renderPaginationControls() {
        const paginationContainers = document.querySelectorAll('.cat-paginacion');

        if (paginationMode === 'infinite') {
            paginationContainers.forEach(container => container.style.display = 'none');
            return;
        }

        const totalPages = Math.max(1, Math.ceil(filteredBooks.length / perPage));

        paginationContainers.forEach(container => {
            container.style.display = totalPages > 1 ? 'block' : 'none';
            if (totalPages <= 1) return;

            const ol = document.createElement('ol');

            // Previous page arrow
            const prevLi = document.createElement('li');
            if (currentPage === 1) {
                prevLi.innerHTML = `<span class="disabled" aria-label="Página anterior" style="opacity: 0.4; cursor: not-allowed; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1.5px solid var(--color-border); border-radius: var(--radius);">&#8592;</span>`;
            } else {
                const prevA = document.createElement('a');
                prevA.href = '#';
                prevA.ariaLabel = 'Página anterior';
                prevA.innerHTML = '&#8592;';
                prevA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage--;
                    updateCatalogue();
                    scrollToGridTop();
                });
                prevLi.appendChild(prevA);
            }
            ol.appendChild(prevLi);

            // Compute sliding window pagination numbers
            const delta = 2;
            let start = Math.max(1, currentPage - delta);
            let end = Math.min(totalPages, currentPage + delta);

            if (end - start < 4 && totalPages >= 5) {
                if (start === 1) {
                    end = Math.min(totalPages, start + 4);
                } else if (end === totalPages) {
                    start = Math.max(1, end - 4);
                }
            }

            if (start > 1) {
                const firstLi = document.createElement('li');
                const firstA = document.createElement('a');
                firstA.href = '#';
                firstA.textContent = '1';
                firstA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = 1;
                    updateCatalogue();
                    scrollToGridTop();
                });
                firstLi.appendChild(firstA);
                ol.appendChild(firstLi);

                if (start > 2) {
                    const dotsLi = document.createElement('li');
                    dotsLi.innerHTML = `<span class="ellipsis" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;">…</span>`;
                    ol.appendChild(dotsLi);
                }
            }

            for (let i = start; i <= end; i++) {
                const pageLi = document.createElement('li');
                const pageA = document.createElement('a');
                pageA.href = '#';
                pageA.textContent = i;
                if (i === currentPage) {
                    pageA.setAttribute('aria-current', 'page');
                }
                pageA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = i;
                    updateCatalogue();
                    scrollToGridTop();
                });
                pageLi.appendChild(pageA);
                ol.appendChild(pageLi);
            }

            if (end < totalPages) {
                if (end < totalPages - 1) {
                    const dotsLi = document.createElement('li');
                    dotsLi.innerHTML = `<span class="ellipsis" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;">…</span>`;
                    ol.appendChild(dotsLi);
                }

                const lastLi = document.createElement('li');
                const lastA = document.createElement('a');
                lastA.href = '#';
                lastA.textContent = totalPages;
                lastA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = totalPages;
                    updateCatalogue();
                    scrollToGridTop();
                });
                lastLi.appendChild(lastA);
                ol.appendChild(lastLi);
            }

            // Next page arrow
            const nextLi = document.createElement('li');
            if (currentPage === totalPages) {
                nextLi.innerHTML = `<span class="disabled" aria-label="Página siguiente" style="opacity: 0.4; cursor: not-allowed; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1.5px solid var(--color-border); border-radius: var(--radius);">&#8594;</span>`;
            } else {
                const nextA = document.createElement('a');
                nextA.href = '#';
                nextA.ariaLabel = 'Página siguiente';
                nextA.innerHTML = '&#8594;';
                nextA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage++;
                    updateCatalogue();
                    scrollToGridTop();
                });
                nextLi.appendChild(nextA);
            }
            ol.appendChild(nextLi);

            container.innerHTML = '';
            container.appendChild(ol);
        });
    }

    function renderSortChips() {
        const chipsContainer = document.querySelector('.cat-chips');
        if (!chipsContainer) return;

        chipsContainer.innerHTML = '';

        const sortOptions = [
            { key: 'new', label: 'Nuevo' },
            { key: 'popular', label: 'Popular' },
            { key: 'price', label: 'Precio' }
        ];

        sortOptions.forEach(opt => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = '#';
            a.className = 'cat-chip';

            let displayLabel = opt.label;
            if (sortKey === opt.key) {
                a.classList.add('cat-chip--activo');
                displayLabel += sortOrder === 'asc' ? ' ↑' : ' ↓';
            }

            a.textContent = displayLabel;
            a.addEventListener('click', (e) => {
                e.preventDefault();
                if (sortKey === opt.key) {
                    // Toggle ascending/descending
                    sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    sortKey = opt.key;
                    // Default sort direction per criteria
                    sortOrder = opt.key === 'price' ? 'asc' : 'desc';
                }
                currentPage = 1;
                loadedInfiniteCount = perPage;
                updateCatalogue();
            });

            li.appendChild(a);
            chipsContainer.appendChild(li);
        });
    }

    function scrollToGridTop() {
        const searchBar = document.querySelector('.cat-barra');
        if (searchBar) {
            searchBar.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // ── 9. State Synchronization & Update Orchestrator ─────────────────────
    function updateCatalogue() {
        // Read form element states to keep variables accurate
        if (minPriceInput) minPrice = minPriceInput.value !== '' ? parseFloat(minPriceInput.value) : null;
        if (maxPriceInput) maxPrice = maxPriceInput.value !== '' ? parseFloat(maxPriceInput.value) : null;
        
        selectedCategories = [];
        document.querySelectorAll('input[name="category[]"]:checked').forEach(cb => {
            selectedCategories.push(cb.value);
        });

        selectedAges = [];
        document.querySelectorAll('input[name="age[]"]:checked').forEach(cb => {
            selectedAges.push(cb.value);
        });

        if (perPageSelect) perPage = parseInt(perPageSelect.value) || 12;
        if (paginacionModeSelect) paginationMode = paginacionModeSelect.value;

        // Correct bounds
        applyFilters();
        
        const totalPages = Math.max(1, Math.ceil(filteredBooks.length / perPage));
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        updateURL();
        renderGrid();
        renderPaginationControls();
        renderSortChips();
    }

    // ── 10. Listeners & Reactive Filtering ────────────────────────────────────
    
    // Intercept form submissions
    if (filterForm) {
        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            currentPage = 1;
            loadedInfiniteCount = perPage;
            updateCatalogue();
            closeSidebar();
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (searchInput) {
                searchQuery = searchInput.value;
                saveRecentSearch(searchQuery);
            }
            currentPage = 1;
            loadedInfiniteCount = perPage;
            updateCatalogue();
            closeSidebar();
        });
    }

    // Clear filters
    const clearBtn = document.querySelector('.cat-btn-limpiar');
    if (clearBtn) {
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            
            searchQuery = '';
            minPrice = null;
            maxPrice = null;
            selectedCategories = [];
            selectedAges = [];
            sortKey = 'new';
            sortOrder = 'desc';
            currentPage = 1;
            loadedInfiniteCount = perPage;

            // Reset inputs
            if (searchInput) searchInput.value = '';
            if (minPriceInput) minPriceInput.value = '';
            if (maxPriceInput) maxPriceInput.value = '';
            
            document.querySelectorAll('input[name="category[]"]').forEach(cb => cb.checked = false);
            document.querySelectorAll('input[name="age[]"]').forEach(cb => cb.checked = false);
            
            updateCatalogue();
            closeSidebar();
        });
    }

    // Search input typing listeners (with debounce to make search smooth and responsive)
    let searchDebounceTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => {
                searchQuery = searchInput.value;
                currentPage = 1;
                loadedInfiniteCount = perPage;
                updateCatalogue();
            }, 300);
        });

        // Add search query to recent searches when the user hits Enter or leaves focus (if non-empty)
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchQuery = searchInput.value;
                saveRecentSearch(searchQuery);
                currentPage = 1;
                loadedInfiniteCount = perPage;
                updateCatalogue();
                closeSidebar();
            }
        });
    }

    // Checkboxes change listeners -> Reactive updating!
    document.querySelectorAll('input[name="category[]"], input[name="age[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            currentPage = 1;
            loadedInfiniteCount = perPage;
            updateCatalogue();
        });
    });

    // Price range typing listeners (reactive with debounce)
    let priceDebounceTimeout;
    [minPriceInput, maxPriceInput].forEach(input => {
        if (input) {
            input.addEventListener('input', () => {
                clearTimeout(priceDebounceTimeout);
                priceDebounceTimeout = setTimeout(() => {
                    currentPage = 1;
                    loadedInfiniteCount = perPage;
                    updateCatalogue();
                }, 400);
            });
        }
    });

    // Pagination per_page & mode selectors
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            currentPage = 1;
            loadedInfiniteCount = perPage;
            updateCatalogue();
        });
    }

    if (paginacionModeSelect) {
        paginacionModeSelect.addEventListener('change', () => {
            currentPage = 1;
            loadedInfiniteCount = perPage;
            updateCatalogue();
        });
    }

    // ── 11. Infinite Scroll Window Events ─────────────────────────────────────
    function handleWindowScroll() {
        if (paginationMode !== 'infinite') return;
        if (loadingMore) return;

        const trigger = document.getElementById('infinite-scroll-trigger');
        if (!trigger) return;

        const rect = trigger.getBoundingClientRect();
        // Check if trigger is visible in the viewport
        if (rect.top <= window.innerHeight + 150) {
            loadMoreBooks();
        }
    }

    function loadMoreBooks() {
        if (loadingMore) return;
        if (loadedInfiniteCount >= filteredBooks.length) return;

        loadingMore = true;
        
        // Simulating minor visual loading delay for sleek feel
        setTimeout(() => {
            loadedInfiniteCount += perPage;
            renderGrid();
            loadingMore = false;
            // Check again in case the new content didn't push the trigger out of the screen
            handleWindowScroll();
        }, 250);
    }

    window.addEventListener('scroll', handleWindowScroll);
    window.addEventListener('resize', handleWindowScroll);

    // Helper to escape HTML characters
    function escapeHtml(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ── 12. Bootstrap / Initialization ───────────────────────────────────────
    syncStateFromURL();
    loadRecentSearches();
    applyFilters();
    renderGrid();
    renderPaginationControls();
    renderSortChips();
});
