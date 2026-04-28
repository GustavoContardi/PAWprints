
<!-- Hero -->
<section class="ind-hero">
    <p class="ind-hero-sub">Nuestros</p>
    <h1 class="ind-hero-titulo">Indispensables</h1>
    <form class="ind-busqueda" action="buscar" method="GET">
        <label for="busqueda" class="sr-only">Buscar</label>
        <input type="search" id="busqueda" name="busqueda" placeholder="Buscar">
        <button type="submit" aria-label="Buscar"></button>
    </form>
</section>

<!-- Novedades -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="catalogo.html">Novedades</a></h2>

    <button class="ind-btn-anterior" type="button" aria-label="Anterior en Novedades" disabled></button>

    <ul class="ind-carrusel">
        <?php foreach($new as $book): ?>
            <li>
                <article class="ind-card">
                    <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro 1">
                    <p class="ind-card-titulo"><?= htmlspecialchars($book['title']) ?></p>
                    <p class="ind-card-precio"><?= htmlspecialchars($book['price']) ?></p>
                    <button class="ind-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <button class="ind-btn-siguiente" type="button" aria-label="Siguiente en Novedades" disabled></button>
    
</section>

<!-- Descuentos -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="catalogo.html">Descuentos</a></h2>
    
    <button class="ind-btn-anterior" type="button" aria-label="Anterior en Descuentos" disabled></button>
    
    <ul class="ind-carrusel">
        <?php foreach($sales as $book): ?>
            <li>
                <article class="ind-card">
                    <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro 1">
                    <p class="ind-card-titulo"><?= htmlspecialchars($book['title']) ?></p>
                    <p class="ind-card-precio"><?= htmlspecialchars($book['price']) ?></p>
                    <button class="ind-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <button class="ind-btn-siguiente" type="button" aria-label="Siguiente en Descuentos" disabled></button>
    
</section>

<!-- Recomendados -->
<section class="ind-seccion">
    <h2 class="ind-seccion-titulo"><a href="catalogo.html">Recomendados</a></h2>
    
    <button class="ind-btn-anterior" type="button" aria-label="Anterior en Recomendados" disabled></button>
    
    <ul class="ind-carrusel">
        <?php foreach($recommended as $book): ?>
            <li>
                <article class="ind-card">
                    <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="Portada de Libro 1">
                    <p class="ind-card-titulo"><?= htmlspecialchars($book['title']) ?></p>
                    <p class="ind-card-precio"><?= htmlspecialchars($book['price']) ?></p>
                    <button class="ind-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <button class="ind-btn-siguiente" type="button" aria-label="Siguiente en Recomendados" disabled></button>
    
</section>
