class PAWprintsCarrousel {
    /**
     * @param {string|HTMLElement} containerSelector - El contenedor principal que recibe la librería
     * @param {Object} options - Opciones de configuración (ej. { effect: 'slide'|'fade'|'zoom' })
     */
    constructor(containerSelector, options = {}) {
        this.container = typeof containerSelector === 'string' 
            ? document.querySelector(containerSelector) 
            : containerSelector;
            
        if (!this.container) return;
        
        this.effect = options.effect || 'slide'; // Efectos posibles: 'slide', 'fade', 'zoom'
        this.items = Array.from(this.container.children);
        if (this.items.length === 0) return;
        
        this.currentIndex = 0;
        this.initDOM();
        this.initPreloader();
    }

    initDOM() {
        // Limpiamos el contenedor original para inyectar nuestra propia estructura
        this.container.innerHTML = '';
        this.container.classList.add('paw-carrousel-container');

        // Contenedor interno para los slides (diapositivas)
        this.slidesWrapper = document.createElement('div');
        this.slidesWrapper.className = `paw-slides-wrapper effect-${this.effect}`;
        this.container.appendChild(this.slidesWrapper);

        // Crear los slides
        this.slides = [];
        this.items.forEach((item, index) => {
            const slide = document.createElement('div');
            slide.className = 'paw-slide';
            if (index === 0) slide.classList.add('active');
            
            // Clonar el contenido del item de forma segura, sin usar innerHTML,
            // para evitar la ejecución de scripts en el contenido copiado.
            const fragment = document.createDocumentFragment();
            Array.from(item.childNodes).forEach(child => fragment.appendChild(child.cloneNode(true)));
            slide.appendChild(fragment);
            
            this.slidesWrapper.appendChild(slide);
            this.slides.push(slide);
        });


        // Capa de UI (botones, thumbs) que inicia oculta
        this.uiWrapper = document.createElement('div');
        this.uiWrapper.className = 'paw-ui-wrapper';
        this.uiWrapper.style.display = 'none'; 
        this.container.appendChild(this.uiWrapper);

        // Barra de progreso y texto
        this.loaderWrapper = document.createElement('div');
        this.loaderWrapper.className = 'paw-loader-wrapper';
        
        this.progressBarContainer = document.createElement('div');
        this.progressBarContainer.className = 'paw-progress-bar-container';
        
        this.progressBar = document.createElement('div');
        this.progressBar.className = 'paw-progress-bar';
        this.progressBar.style.width = '0%';
        
        this.loaderText = document.createElement('div');
        this.loaderText.className = 'paw-loader-text';
        this.loaderText.textContent = 'Cargando... 0%';

        this.progressBarContainer.appendChild(this.progressBar);
        this.loaderWrapper.appendChild(this.loaderText);
        this.loaderWrapper.appendChild(this.progressBarContainer);
        this.container.appendChild(this.loaderWrapper);

        // Crear dots de paginación
        this.dotsWrapper = document.createElement('div');
        this.dotsWrapper.className = 'paw-dots-wrapper';
        this.dots = [];
        this.items.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.className = 'paw-dot';
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => this.goTo(index));
            this.dotsWrapper.appendChild(dot);
            this.dots.push(dot);
        });
        this.container.appendChild(this.dotsWrapper);

        this.setupNavigation();
    }

    setupNavigation() {
        // 1. Botones (Anterior / Siguiente)
        this.prevBtn = document.createElement('button');
        this.prevBtn.className = 'paw-btn-prev';
        this.prevBtn.innerHTML = '&#10094;'; // Flecha izquierda
        this.prevBtn.setAttribute('aria-label', 'Anterior');
        this.prevBtn.addEventListener('click', () => this.prev());

        this.nextBtn = document.createElement('button');
        this.nextBtn.className = 'paw-btn-next';
        this.nextBtn.innerHTML = '&#10095;'; // Flecha derecha
        this.nextBtn.setAttribute('aria-label', 'Siguiente');
        this.nextBtn.addEventListener('click', () => this.next());

        this.uiWrapper.appendChild(this.prevBtn);
        this.uiWrapper.appendChild(this.nextBtn);

        // 2. Thumbs (Miniaturas)
        this.thumbsWrapper = document.createElement('div');
        this.thumbsWrapper.className = 'paw-thumbs-wrapper';
        this.thumbs = [];
        
        this.items.forEach((item, index) => {
            const img = item.querySelector('img');
            if (img) {
                const thumb = document.createElement('img');
                thumb.src = img.src;
                thumb.className = 'paw-thumb';
                if (index === 0) thumb.classList.add('active');
                thumb.addEventListener('click', () => this.goTo(index));
                this.thumbsWrapper.appendChild(thumb);
                this.thumbs.push(thumb);
            }
        });
        
        this.uiWrapper.appendChild(this.thumbsWrapper);

        // 3. Teclado (Flechas)
        document.addEventListener('keydown', (e) => {
            // Solo actuar si el carrusel está visible o si hay lógica para verificar foco
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });

        // 4. Swipe (Táctil)
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.container.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.container.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe(touchStartX, touchEndX);
        }, { passive: true });
    }

    handleSwipe(start, end) {
        const threshold = 50; // mínimo movimiento en px
        if (start - end > threshold) this.next(); // Swipe izquierda -> Siguiente
        if (end - start > threshold) this.prev(); // Swipe derecha -> Anterior
    }

    initPreloader() {
        if (typeof PAWprintsPreloaderIMG !== 'undefined') {
            // Instanciar la librería secundaria para el progreso
            new PAWprintsPreloaderIMG(this.slidesWrapper, 
                (percent) => {
                    this.progressBar.style.width = percent + '%';
                    this.loaderText.textContent = `Cargando... ${percent}%`;
                }, 
                () => {
                    // Al 100%, iniciar
                    setTimeout(() => {
                        this.loaderWrapper.style.display = 'none';
                        this.uiWrapper.style.display = 'block'; // Mostrar navegación
                        this.startAnimation(); // Activar animaciones
                    }, 600); // Pequeño retraso visual
                }
            );
        } else {
            console.warn('PAWprintsPreloaderIMG no encontrada. Omitiendo barra de progreso.');
            this.loaderWrapper.style.display = 'none';
            this.uiWrapper.style.display = 'block';
            this.startAnimation();
        }
    }

    startAnimation() {
        this.container.classList.add('is-ready');
    }

    goTo(index) {
        index = parseInt(index, 10);
        if (isNaN(index)) return;
        if (index < 0) index = this.slides.length - 1;
        if (index >= this.slides.length) index = 0;
        
        // Remover clase activa del actual
        this.slides.at(this.currentIndex).classList.remove('active');
        if (this.thumbs.at(this.currentIndex)) {
            this.thumbs.at(this.currentIndex).classList.remove('active');
        }
        if (this.dots && this.dots.at(this.currentIndex)) {
            this.dots.at(this.currentIndex).classList.remove('active');
        }
        
        // Actualizar índice
        this.currentIndex = index;
        
        // Agregar clase activa al nuevo
        this.slides.at(this.currentIndex).classList.add('active');
        if (this.thumbs.at(this.currentIndex)) {
            this.thumbs.at(this.currentIndex).classList.add('active');
        }
        if (this.dots && this.dots.at(this.currentIndex)) {
            this.dots.at(this.currentIndex).classList.add('active');
        }
    }

    prev() {
        this.goTo(this.currentIndex - 1);
    }

    next() {
        this.goTo(this.currentIndex + 1);
    }
}
