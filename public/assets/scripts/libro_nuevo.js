/**
 * PAWprints — libro_nuevo.js
 * Validación client-side del formulario de carga de libros nuevos.
 */

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-libro-nuevo');
    if (!form) return;

    const fields = {
        title: document.getElementById('title'),
        author: document.getElementById('author'),
        price: document.getElementById('price'),
        stock: document.getElementById('stock'),
        discount: document.getElementById('discount'),
        age: document.getElementById('age'),
        image: document.getElementById('image')
    };

    /**
     * Valida un campo individualmente y retorna el mensaje de error o cadena vacía.
     */
    function validateField(name, value, file = null) {
        let error = '';
        
        switch (name) {
            case 'title':
                if (!value || value.trim() === '') {
                    error = 'El título es requerido.';
                } else if (value.length > 255) {
                    error = 'El título no puede superar los 255 caracteres.';
                }
                break;
                
            case 'author':
                if (!value || value.trim() === '') {
                    error = 'El autor es requerido.';
                } else if (value.length > 255) {
                    error = 'El autor no puede superar los 255 caracteres.';
                }
                break;
                
            case 'price':
                if (value === '') {
                    error = 'El precio es requerido.';
                } else {
                    const num = Number(value);
                    if (isNaN(num)) {
                        error = 'El precio debe ser un número válido.';
                    } else if (num < 0) {
                        error = 'El precio debe ser mayor o igual a 0.';
                    }
                }
                break;
                
            case 'stock':
                if (value === '') {
                    error = 'El stock es requerido.';
                } else {
                    const num = Number(value);
                    if (isNaN(num)) {
                        error = 'El stock debe ser un número válido.';
                    } else if (!Number.isInteger(num)) {
                        error = 'El stock debe ser un número entero.';
                    } else if (num < 0) {
                        error = 'El stock debe ser mayor o igual a 0.';
                    }
                }
                break;
                
            case 'discount':
                if (value !== '') {
                    const num = Number(value);
                    if (isNaN(num)) {
                        error = 'El descuento debe ser un número válido.';
                    } else if (num < 0 || num > 100) {
                        error = 'El descuento debe estar entre 0 y 100.';
                    }
                }
                break;
                
            case 'image':
                if (file) {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    const allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                    const ext = file.name.split('.').pop().toLowerCase();
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    
                    if (!allowedTypes.includes(file.type) && !allowedExts.includes(ext)) {
                        error = 'Formatos permitidos: JPG, JPEG, PNG, WEBP.';
                    } else if (file.size > maxSize) {
                        error = 'La imagen no debe superar los 2MB.';
                    }
                }
                break;
        }
        
        return error;
    }

    /**
     * Muestra el mensaje de error inline y resalta el input.
     */
    function showError(fieldName, message) {
        const errorSpan = document.getElementById(`error-${fieldName}`);
        const field = fields[fieldName];
        if (!field) return;
        
        const group = field.closest('.form-group');
        if (errorSpan) {
            errorSpan.textContent = message;
            errorSpan.classList.add('visible');
        }
        if (group) {
            group.classList.add('has-error');
            group.classList.remove('is-valid');
        }
    }

    /**
     * Oculta el mensaje de error y agrega estilo de campo válido si corresponde.
     */
    function clearError(fieldName) {
        const errorSpan = document.getElementById(`error-${fieldName}`);
        const field = fields[fieldName];
        if (!field) return;
        
        const group = field.closest('.form-group');
        if (errorSpan) {
            errorSpan.textContent = '';
            errorSpan.classList.remove('visible');
        }
        if (group) {
            group.classList.remove('has-error');
            
            // Agregar clase 'is-valid' si tiene contenido válido o si es opcional y no tiene error
            const val = field.type === 'file' ? field.files[0] : field.value;
            const requiredFields = ['title', 'author', 'price', 'stock'];
            if (requiredFields.includes(fieldName) || (val && val !== '')) {
                group.classList.add('is-valid');
            } else {
                group.classList.remove('is-valid');
            }
        }
    }

    /**
     * Validador en evento Blur (salir del foco del campo).
     */
    function handleBlur(fieldName) {
        const field = fields[fieldName];
        if (!field) return;
        
        const val = field.value;
        const file = field.type === 'file' ? field.files[0] : null;
        const error = validateField(fieldName, val, file);
        
        if (error) {
            showError(fieldName, error);
        } else {
            clearError(fieldName);
        }
    }

    /**
     * Validador en tiempo real (evento input) sólo si el campo ya tenía un error.
     */
    function handleInput(fieldName) {
        const field = fields[fieldName];
        if (!field) return;
        
        const group = field.closest('.form-group');
        if (group && group.classList.contains('has-error')) {
            const val = field.value;
            const file = field.type === 'file' ? field.files[0] : null;
            const error = validateField(fieldName, val, file);
            if (!error) {
                clearError(fieldName);
            } else {
                // Si sigue habiendo error, actualiza el mensaje por si cambió de tipo
                const errorSpan = document.getElementById(`error-${fieldName}`);
                if (errorSpan) {
                    errorSpan.textContent = error;
                }
            }
        }
    }

    // ── Open Library Auto-fill ──────────────────────────────────────────────
    const olQuery = document.getElementById('ol-query');
    const olSearchBtn = document.getElementById('ol-search-btn');
    const olResults = document.getElementById('ol-results');
    const coverIdInput = document.getElementById('cover_id');
    const coverPreview = document.getElementById('cover-preview');

    if (olQuery && olSearchBtn && olResults) {
        olSearchBtn.addEventListener('click', async () => {
            const q = olQuery.value.trim();
            if (!q) return;

            olSearchBtn.disabled = true;
            olSearchBtn.textContent = 'Buscando...';
            olResults.innerHTML = '';
            olResults.classList.remove('visible');

            try {
                const res = await fetch(`/api/search-book?q=${encodeURIComponent(q)}`);
                const data = await res.json();

                olResults.classList.add('visible');

                if (!data.results || data.results.length === 0) {
                    olResults.innerHTML = '<div class="ol-no-results">Sin resultados.</div>';
                    return;
                }

                data.results.forEach((book, idx) => {
                    const item = document.createElement('div');
                    item.className = 'ol-result-item';
                    item.tabIndex = 0;
                    item.setAttribute('role', 'button');
                    item.dataset.index = idx;

                    const img = book.cover_url
                        ? `<img src="${book.cover_url}" alt="" loading="lazy">`
                        : '<div style="width:48px;height:72px;background:var(--color-border);border-radius:4px;flex-shrink:0"></div>';

                    const year = book.first_publish_year ? `(${book.first_publish_year})` : '';

                    item.innerHTML = `
                        ${img}
                        <div class="ol-result-info">
                            <p class="ol-result-title">${escapeHtml(book.title)}</p>
                            <p class="ol-result-author">${escapeHtml(book.author)} ${year}</p>
                        </div>
                    `;

                    item.addEventListener('click', () => fillBookData(book));
                    item.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            fillBookData(book);
                        }
                    });

                    olResults.appendChild(item);
                });
            } catch (err) {
                olResults.classList.add('visible');
                olResults.innerHTML = '<div class="ol-error">Error al conectar con Open Library.</div>';
            } finally {
                olSearchBtn.disabled = false;
                olSearchBtn.textContent = 'Buscar';
            }
        });

        olQuery.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                olSearchBtn.click();
            }
        });
    }

    function fillBookData(book) {
        const titleField = document.getElementById('title');
        const authorField = document.getElementById('author');
        const descField = document.getElementById('description');
        const categoryField = document.getElementById('category');

        if (titleField && book.title) {
            titleField.value = book.title;
            titleField.dispatchEvent(new Event('input'));
        }
        if (authorField && book.author) {
            authorField.value = book.author;
            authorField.dispatchEvent(new Event('input'));
        }
        if (descField && book.description) {
            descField.value = book.description;
            descField.dispatchEvent(new Event('input'));
        }

        // Mapear subjects a categoría local
        if (categoryField && book.subjects && book.subjects.length > 0) {
            const mapped = mapSubjectToCategory(book.subjects);
            if (mapped) {
                categoryField.value = mapped;
            }
        }

        // Guardar cover_id para recuperar portada al persistir
        if (coverIdInput && book.cover_i) {
            coverIdInput.value = book.cover_i;
        }

        // Mostrar preview de portada
        if (coverPreview && book.cover_url) {
            coverPreview.innerHTML = `<img src="${book.cover_url}" alt="Portada prevista">`;
            coverPreview.classList.add('visible');
        } else if (coverPreview) {
            coverPreview.innerHTML = '';
            coverPreview.classList.remove('visible');
        }

        // Scroll al formulario
        const form = document.getElementById('form-libro-nuevo');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function mapSubjectToCategory(subjects) {
        const subjectLower = subjects.map(s => s.toLowerCase()).join(' ');
        if (/\b(fiction|novel|story|fantasy|mystery|romance|thriller|science fiction|horror)\b/.test(subjectLower)) {
            return 'Ficción';
        }
        if (/\b(history|science|biography|philosophy|essay|cooking|travel|art|music|non-fiction|nonfiction|technology)\b/.test(subjectLower)) {
            return 'No ficción';
        }
        if (/\b(children|juvenile|picture book|kids|infantil)\b/.test(subjectLower)) {
            return 'Infantil';
        }
        if (/\b(young adult|teen|ya |adolescent)\b/.test(subjectLower)) {
            return 'Juvenil';
        }
        if (/\b(textbook|education|academic|school|study|university|college)\b/.test(subjectLower)) {
            return 'Académico';
        }
        return '';
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Asociar escuchas de eventos a cada campo
    Object.keys(fields).forEach(fieldName => {
        const field = fields[fieldName];
        if (!field) return;

        field.addEventListener('blur', () => handleBlur(fieldName));
        field.addEventListener('input', () => handleInput(fieldName));
        
        if (field.type === 'file') {
            field.addEventListener('change', () => handleBlur(fieldName));
        }
    });

    // Validar en el submit general
    form.addEventListener('submit', (e) => {
        let hasErrors = false;
        
        Object.keys(fields).forEach(fieldName => {
            const field = fields[fieldName];
            if (!field) return;
            
            const val = field.value;
            const file = field.type === 'file' ? field.files[0] : null;
            const error = validateField(fieldName, val, file);
            
            if (error) {
                showError(fieldName, error);
                hasErrors = true;
            } else {
                clearError(fieldName);
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            // Scroll suave hacia el primer campo con error
            const firstErrorGroup = form.querySelector('.form-group.has-error');
            if (firstErrorGroup) {
                firstErrorGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
});
