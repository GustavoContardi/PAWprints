<aside class="cat-sidebar">
    <form class="cat-filtros" method="get" action="/catalogo">

        <p class="cat-filtros-titulo">Keywords</p>
        <ul class="cat-keywords">
            <li class="cat-keyword">Spring <button type="button">×</button></li>
            <li class="cat-keyword">Smart <button type="button">×</button></li>
            <li class="cat-keyword">Modern <button type="button">×</button></li>
        </ul>

        <label for="price-range" class="cat-filtro-label">
            Precio <span id="rangeValue">$0-<?= htmlspecialchars($_GET['precio'] ?? '100') ?></span>
        </label>
        <input type="range" id="price-range" name="precio" min="0" max="100" value="<?= htmlspecialchars($_GET['precio'] ?? '100') ?>" step="1">

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Género</legend>
            <?php 
                $categorias_seleccionadas = $_GET['categoria'] ?? [];
                $categorias = [
                    'ciencia-ficcion' => 'Ciencia ficción',
                    'romance' => 'Romance',
                    'aventura' => 'Aventura',
                    'fantasia' => 'Fantasía',
                    'misterio' => 'Misterio',
                    'historia' => 'Historia',
                    'no-ficcion' => 'No Ficción',
                    'otros' => 'Otros'
                ];
                foreach ($categorias as $value => $label): 
                    $checked = in_array($value, (array)$categorias_seleccionadas) ? 'checked' : '';
            ?>
                <label><input type="checkbox" name="categoria[]" value="<?= $value ?>" <?= $checked ?>> <?= $label ?></label>
            <?php endforeach; ?>
        </fieldset>

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Edad</legend>
            <?php 
                $edades_seleccionadas = $_GET['edad'] ?? [];
                $edades = [
                    'infantil' => 'Infantil',
                    'juvenil' => 'Juvenil',
                    'adulto' => 'Adulto'
                ];
                foreach ($edades as $value => $label): 
                    $checked = in_array($value, (array)$edades_seleccionadas) ? 'checked' : '';
            ?>
                <label><input type="checkbox" name="edad[]" value="<?= $value ?>" <?= $checked ?>> <?= $label ?></label>
            <?php endforeach; ?>
        </fieldset>

        <button type="submit" class="cat-btn-aplicar">Aplicar Filtros</button>
        <a href="/catalogo" class="cat-btn-limpiar">Limpiar</a>
    </form>
</aside>

<section class="cat-contenido">

    <search class="cat-barra">
        <button class="cat-btn-filtro" type="button" aria-label="Filtros"></button>

        <form class="cat-busqueda" method="get" action="/catalogo">
            <label for="busqueda" class="sr-only">Buscar</label>
            <input type="search" id="busqueda" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>" placeholder="Buscar">
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
        <?php if (empty($books)): ?>
            <p>No se encontraron libros con los filtros seleccionados.</p>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <article class="cat-card">
                    <img src="/assets/img/<?= htmlspecialchars($book['imagen'] ?? 'placeholder.jpg') ?>" alt="Portada del libro">
                    <h3><?= htmlspecialchars($book['titulo']) ?></h3>
                    <p class="cat-card-autor"><?= htmlspecialchars($book['autor']) ?></p>
                    <p class="cat-card-precio">$<?= number_format($book['precio'], 2, ',', '.') ?></p>
                    <button class="cat-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

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