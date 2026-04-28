<aside class="cat-sidebar">
    <form class="cat-filtros" method="get" action="/catalogo/filtrar">

        <p class="cat-filtros-titulo">Keywords</p>
        <ul class="cat-keywords">
            <li class="cat-keyword">Spring <button type="button">×</button></li>
            <li class="cat-keyword">Smart <button type="button">×</button></li>
            <li class="cat-keyword">Modern <button type="button">×</button></li>
        </ul>

        <label for="price-range" class="cat-filtro-label">
            Precio <span id="rangeValue">$0-100</span>
        </label>
        <input type="range" id="price-range" name="precio" min="0" max="100" value="100" step="1">

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Género</legend>
            <label><input type="checkbox" name="categoria" value="ciencia-ficcion" checked> Ciencia ficción</label>
            <label><input type="checkbox" name="categoria" value="romance" checked> Romance</label>
            <label><input type="checkbox" name="categoria" value="aventura" checked> Aventura</label>
            <label><input type="checkbox" name="categoria" value="fantasia"> Fantasía</label>
            <label><input type="checkbox" name="categoria" value="misterio"> Misterio</label>
            <label><input type="checkbox" name="categoria" value="historia"> Historia</label>
            <label><input type="checkbox" name="categoria" value="no-ficcion"> No Ficción</label>
            <label><input type="checkbox" name="categoria" value="otros"> Otros</label>
        </fieldset>

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Edad</legend>
            <label><input type="checkbox" name="edad" value="infantil" checked> Infantil</label>
            <label><input type="checkbox" name="edad" value="juvenil" checked> Juvenil</label>
            <label><input type="checkbox" name="edad" value="adulto" checked> Adulto</label>
        </fieldset>

    </form>
</aside>

<section class="cat-contenido">

    <search class="cat-barra">
        <button class="cat-btn-filtro" type="button" aria-label="Filtros"></button>

        <form class="cat-busqueda" method="get" action="/catalogo/buscar">
            <label for="busqueda" class="sr-only">Buscar</label>
            <input type="search" id="busqueda" name="busqueda" placeholder="Buscar">
            <button type="submit" aria-label="Buscar"></button>
        </form>

        <menu class="cat-chips">
            <li><button class="cat-chip cat-chip--activo" type="button">✓ Nuevo</button></li>
            <li><button class="cat-chip" type="button">Género</button></li>
            <li><button class="cat-chip" type="button">Popular</button></li>
            <li><button class="cat-chip" type="button">Precio</button></li>
        </menu>
    </search>

    <nav class="cat-paginacion" aria-label="Paginación superior">
        <ol>
            <li><a href="?pagina=1" aria-label="Página anterior">&#8592;</a></li>
            <li><a href="?pagina=1" aria-current="page">1</a></li>
            <li><a href="?pagina=2">2</a></li>
            <li><a href="?pagina=3">3</a></li>
            <li><a href="?pagina=4">4</a></li>
            <li><a href="?pagina=5">5</a></li>
            <li><a href="?pagina=2" aria-label="Página siguiente">&#8594;</a></li>
        </ol>
    </nav>

    <section class="cat-grid">
        <?php foreach ($books as $book): ?>
            <article class="cat-card">
                <img src="/assets/img/<?= htmlspecialchars($book['imagen']) ?>" alt="Portada del book$book">
                <h3><?= htmlspecialchars($book['titulo']) ?></h3>
                <p class="cat-card-autor"><?= htmlspecialchars($book['autor']) ?></p>
                <p class="cat-card-precio">$<?= number_format($book['precio'], 2, ',', '.') ?></p>
                <button class="cat-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
            </article>
        <?php endforeach; ?>

    </section>

    <nav class="cat-paginacion" aria-label="Paginación">
        <ol>
            <li><a href="?pagina=1" aria-label="Página anterior">&#8592;</a></li>
            <li><a href="?pagina=1" aria-current="page">1</a></li>
            <li><a href="?pagina=2">2</a></li>
            <li><a href="?pagina=3">3</a></li>
            <li><a href="?pagina=4">4</a></li>
            <li><a href="?pagina=5">5</a></li>
            <li><a href="?pagina=2" aria-label="Página siguiente">&#8594;</a></li>
        </ol>
    </nav>

</section>