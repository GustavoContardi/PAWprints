class PAWprintsPreloaderIMG {
    /**
     * @param {string|HTMLElement} containerSelector - Contenedor con las imágenes a precargar
     * @param {function} onProgress - Callback ejecutado en cada avance, recibe el % (0-100)
     * @param {function} onComplete - Callback ejecutado al completar el 100%
     */
    constructor(containerSelector, onProgress, onComplete) {
        this.container = typeof containerSelector === 'string' 
            ? document.querySelector(containerSelector) 
            : containerSelector;
            
        if (!this.container) {
            console.error('PAWprintsPreloaderIMG: Contenedor no encontrado.');
            return;
        }

        // Busca todas las imágenes reales dentro del contenedor
        this.images = Array.from(this.container.querySelectorAll('img'));
        this.totalImages = this.images.length;
        this.loadedImages = 0;
        this.onProgress = onProgress || function(){};
        this.onComplete = onComplete || function(){};

        this.init();
    }

    init() {
        if (this.totalImages === 0) {
            this.onProgress(100);
            this.onComplete();
            return;
        }

        this.images.forEach(img => {
            if (img.complete) {
                this.imageLoaded();
            } else {
                const dummyImg = new Image();
                dummyImg.onload = () => this.imageLoaded();
                dummyImg.onerror = () => this.imageLoaded(); // Se contabiliza igual si hay error para no trabar
                dummyImg.src = img.src;
            }
        });
    }

    imageLoaded() {
        this.loadedImages++;
        const percent = Math.floor((this.loadedImages / this.totalImages) * 100);
        this.onProgress(percent);
        
        if (this.loadedImages === this.totalImages) {
            this.onComplete();
        }
    }
}
