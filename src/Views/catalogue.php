<aside class="cat-sidebar">
    <form class="cat-filtros" method="get" action="/catalogue">

        <!--
        <p class="cat-filtros-titulo">Keywords</p>
    
        <ul class="cat-keywords">
            <li class="cat-keyword">Spring <button type="button">×</button></li>
            <li class="cat-keyword">Smart <button type="button">×</button></li>
            <li class="cat-keyword">Modern <button type="button">×</button></li>
        </ul>
        -->

        <p class="cat-titulo">Filtros</p>

        <label for="price-range" class="cat-filtro-label">
            Precio <span id="rangeValue">$0-<?= htmlspecialchars($_GET['price'] ?? '100') ?></span>
        </label>
        <input type="range" id="price-range" name="price" min="0" max="100" value="<?= htmlspecialchars($_GET['price'] ?? '100') ?>" step="1">

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Género</legend>
            <?php 
                $selected_categories = $_GET['category'] ?? [];
                $categories = [
                    'ciencia-ficcion' => 'Ciencia ficción',
                    'romance' => 'Romance',
                    'aventura' => 'Aventura',
                    'fantasia' => 'Fantasía',
                    'misterio' => 'Misterio',
                    'historia' => 'Historia',
                    'no-ficcion' => 'No Ficción',
                    'otros' => 'Otros'
                ];
                foreach ($categories as $value => $label): 
                    $checked = in_array($value, (array)$selected_categories) ? 'checked' : '';
            ?>
                <label><input type="checkbox" name="category[]" value="<?= $value ?>" <?= $checked ?>> <?= $label ?></label>
            <?php endforeach; ?>
        </fieldset>

        <fieldset class="cat-checks">
            <legend class="cat-filtro-label">Edad</legend>
            <?php 
                $selected_ages = $_GET['age'] ?? [];
                $ages = [
                    'infantil' => 'Infantil',
                    'juvenil' => 'Juvenil',
                    'adulto' => 'Adulto'
                ];
                foreach ($ages as $value => $label): 
                    $checked = in_array($value, (array)$selected_ages) ? 'checked' : '';
            ?>
                <label><input type="checkbox" name="age[]" value="<?= $value ?>" <?= $checked ?>> <?= $label ?></label>
            <?php endforeach; ?>
        </fieldset>

        <button type="submit" class="cat-btn-aplicar">Aplicar Filtros</button>
        <a href="/catalogue" class="cat-btn-limpiar">Limpiar</a>
    </form>
</aside>

<section class="cat-contenido">

    <search class="cat-barra">
        <button class="cat-btn-filtro" type="button" aria-label="Filtros"></button>

        <form class="cat-busqueda" method="get" action="/catalogue">
            <label for="busqueda" class="sr-only">Buscar</label>
            <input type="search" id="busqueda" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Buscar">
            <button type="submit" aria-label="Buscar"></button>
        </form>

        <menu class="cat-chips">
            <?php
                $current_order = $_GET['order'] ?? 'new';
                $order_options = [
                    'new' => 'Nuevo',
                    'popular' => 'Popular',
                    'price' => 'Precio'
                ];
                $selected_categories = $_GET['category'] ?? [];
            ?>
            <?php foreach ($order_options as $valor => $texto): ?>
                <?php
                    $params = $_GET;
                    $params['order'] = $valor;
                ?>
                <li>
                    <a href="?<?= http_build_query($params) ?>" 
                       class="cat-chip <?= $current_order === $valor ? 'cat-chip--activo' : '' ?>">
                        <?= $texto ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li><button class="cat-chip" type="button">Género</button></li>
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
                    <a href="/book/<?= $book['id'] ?>">
                        <img src="/assets/img/<?= htmlspecialchars($book['image'] ?? 'placeholder.jpg') ?>" alt="Portada del libro">
                    </a>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="cat-card-author"><?= htmlspecialchars($book['author']) ?></p>
                    <p class="cat-card-price">$<?= number_format($book['price'], 2, ',', '.') ?></p>
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