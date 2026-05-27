
<link rel="stylesheet" href="/assets/estilos/PAWprintsCarrousel.css">

<!-- Hero -->
<section class="ind-hero">
    <p class="ind-hero-sub">Nuestros</p>
    <h1 class="ind-hero-titulo">Indispensables</h1>
    <form class="ind-busqueda" action="/catalogue" method="GET">
        <label for="busqueda" class="sr-only">Buscar</label>
        <input type="search" id="busqueda" name="search" placeholder="Buscar">
        <button type="submit" aria-label="Buscar"></button>
    </form>
</section>

<!-- Novedades -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="/catalogue">Novedades</a></h2>

    <ul class="ind-carrusel">
        <?php foreach($new as $book): ?>
            <li>
                <article class="ind-card-slider">
                    <div class="card-slider-img">
                        <a href="/book/<?= $book['id'] ?>">
                            <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro">
                        </a>
                    </div>
                    <div class="card-slider-content">
                        <a href="/reserve/<?= $book['id'] ?>" class="ind-btn-carrito card-slider-btn-floating" aria-label="Reservar libro"></a>
                        
                        <div class="card-slider-header">
                            <h3 class="card-slider-title"><?= htmlspecialchars($book['title']) ?></h3>
                        </div>
                        
                        <div class="card-slider-chips">
                            <span class="chip"><?= htmlspecialchars($book['category'] ?? 'Ficción') ?></span>
                        </div>
                        
                        <p class="card-slider-price">$<?= number_format($book['price'], 2, ',', '.') ?></p>
                        <p class="card-slider-author"><?= htmlspecialchars($book['author'] ?? 'Autor Desconocido') ?></p>
                        
                        <div class="card-slider-options">
                            <div class="option-group">
                                <span class="option-label">Formato:</span>
                                <span class="option-value">PDF, EPUB</span>
                            </div>
                            <div class="option-group">
                                <span class="option-label">Idioma:</span>
                                <span class="option-value">Español, Inglés</span>
                            </div>
                        </div>
                        
                        <div class="card-slider-synopsis">
                            <h4 class="synopsis-title">Sinopsis</h4>
                            <p><?= htmlspecialchars($book['description'] ?? 'Sin sinopsis disponible.') ?></p>
                        </div>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    
</section>

<!-- Descuentos -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="/catalogue">Descuentos</a></h2>
    

    
                            <ul class="ind-carrusel" data-effect="fade">
        <?php foreach($sales as $book): ?>
            <li>
                <article class="ind-card-slider">
                    <div class="card-slider-img">
                        <a href="/book/<?= $book['id'] ?>">
                            <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro">
                        </a>
                    </div>
                    <div class="card-slider-content">
                        <a href="/reserve/<?= $book['id'] ?>" class="ind-btn-carrito card-slider-btn-floating" aria-label="Reservar libro"></a>
                        <div class="card-slider-header">
                            <h3 class="card-slider-title"><?= htmlspecialchars($book['title']) ?></h3>
                        </div>
                        <div class="card-slider-chips">
                            <span class="chip"><?= htmlspecialchars($book['category'] ?? 'Ficción') ?></span>
                        </div>
                        <p class="card-slider-price">$<?= number_format($book['price'], 2, ',', '.') ?></p>
                        <p class="card-slider-author"><?= htmlspecialchars($book['author'] ?? 'Autor Desconocido') ?></p>
                        <div class="card-slider-options">
                            <div class="option-group"><span class="option-label">Formato:</span> <span class="option-value">PDF, EPUB</span></div>
                            <div class="option-group"><span class="option-label">Idioma:</span> <span class="option-value">Español, Inglés</span></div>
                        </div>
                        <div class="card-slider-synopsis">
                            <h4 class="synopsis-title">Sinopsis</h4>
                            <p><?= htmlspecialchars($book['description'] ?? 'Sin sinopsis disponible.') ?></p>
                        </div>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    

    
</section>

<!-- Recomendados -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="/catalogue">Recomendados</a></h2>
    

    
                            <ul class="ind-carrusel" data-effect="slide">
        <?php foreach($recommended as $book): ?>
            <li>
                <article class="ind-card-slider">
                    <div class="card-slider-img">
                        <a href="/book/<?= $book['id'] ?>">
                            <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro">
                        </a>
                    </div>
                    <div class="card-slider-content">
                        <a href="/reserve/<?= $book['id'] ?>" class="ind-btn-carrito card-slider-btn-floating" aria-label="Reservar libro"></a>
                        <div class="card-slider-header">
                            <h3 class="card-slider-title"><?= htmlspecialchars($book['title']) ?></h3>
                        </div>
                        <div class="card-slider-chips">
                            <span class="chip"><?= htmlspecialchars($book['category'] ?? 'Ficción') ?></span>
                        </div>
                        <p class="card-slider-price">$<?= number_format($book['price'], 2, ',', '.') ?></p>
                        <p class="card-slider-author"><?= htmlspecialchars($book['author'] ?? 'Autor Desconocido') ?></p>
                        <div class="card-slider-options">
                            <div class="option-group"><span class="option-label">Formato:</span> <span class="option-value">PDF, EPUB</span></div>
                            <div class="option-group"><span class="option-label">Idioma:</span> <span class="option-value">Español, Inglés</span></div>
                        </div>
                        <div class="card-slider-synopsis">
                            <h4 class="synopsis-title">Sinopsis</h4>
                            <p><?= htmlspecialchars($book['description'] ?? 'Sin sinopsis disponible.') ?></p>
                        </div>
                    </div>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    

    
</section>

<!-- Scripts del Nuevo Carrusel Fullscreen -->
<script src="/assets/scripts/PAWprintsPreloaderIMG.js"></script>
<script src="/assets/scripts/PAWprintsCarrousel.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const carruseles = document.querySelectorAll('.ind-carrusel');
    carruseles.forEach(c => {
        const effect = c.dataset.effect || 'zoom';
        new PAWprintsCarrousel(c, { effect });
    });
});
</script>
