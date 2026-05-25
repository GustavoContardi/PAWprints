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
