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

        <label class="cat-filtro-label">Precio ($)</label>
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem;">
            <input type="number" name="min_price" min="0" placeholder="Mín." value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" style="width: 100%; padding: 0.4rem; border: 1.5px solid var(--color-border); border-radius: var(--radius); font-family: var(--font);">
            <span>-</span>
            <input type="number" name="max_price" min="0" placeholder="Máx." value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" style="width: 100%; padding: 0.4rem; border: 1.5px solid var(--color-border); border-radius: var(--radius); font-family: var(--font);">
        </div>
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
        </menu>
    </search>

    <?php
    // ── Helper de paginación ─────────────────────────────────────────────
    // Genera el bloque <nav> de paginación dinámica.
    // Recibe $page, $totalPages y un aria-label para accesibilidad.
    function renderPagination(int $page, int $totalPages, string $ariaLabel = 'Paginación'): void
    {
        if ($totalPages <= 1) return;

        // Preservar todos los query params actuales, cambiando solo "page"
        $buildUrl = function (int $p) {
            $params = $_GET;
            $params['page'] = $p;
            return '?' . http_build_query($params);
        };

        // Rango de páginas a mostrar (ventana deslizante de hasta 5)
        $delta = 2;
        $start = max(1, $page - $delta);
        $end   = min($totalPages, $page + $delta);

        // Ajustar para mostrar siempre al menos 5 si hay suficientes páginas
        if ($end - $start < 4 && $totalPages >= 5) {
            if ($start === 1) {
                $end = min($totalPages, $start + 4);
            } elseif ($end === $totalPages) {
                $start = max(1, $end - 4);
            }
        }

        echo '<nav class="cat-paginacion" aria-label="' . htmlspecialchars($ariaLabel) . '">';
        echo '<ol>';

        // Flecha izquierda
        if ($page === 1) {
            echo '<li><span class="disabled" aria-label="Página anterior">&#8592;</span></li>';
        } else {
            echo '<li><a href="' . $buildUrl($page - 1) . '" aria-label="Página anterior">&#8592;</a></li>';
        }

        // Números de página
        if ($start > 1) {
            echo '<li><a href="' . $buildUrl(1) . '">1</a></li>';
            if ($start > 2) {
                echo '<li><span class="ellipsis">…</span></li>';
            }
        }

        for ($i = $start; $i <= $end; $i++) {
            if ($i === $page) {
                echo '<li><a href="' . $buildUrl($i) . '" aria-current="page">' . $i . '</a></li>';
            } else {
                echo '<li><a href="' . $buildUrl($i) . '">' . $i . '</a></li>';
            }
        }

        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<li><span class="ellipsis">…</span></li>';
            }
            echo '<li><a href="' . $buildUrl($totalPages) . '">' . $totalPages . '</a></li>';
        }

        // Flecha derecha
        if ($page === $totalPages) {
            echo '<li><span class="disabled" aria-label="Página siguiente">&#8594;</span></li>';
        } else {
            echo '<li><a href="' . $buildUrl($page + 1) . '" aria-label="Página siguiente">&#8594;</a></li>';
        }

        echo '</ol>';
        echo '</nav>';
    }
    ?>

    <?php renderPagination($page, $totalPages, 'Paginación superior'); ?>

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
                    <p class="cat-card-autor"><?= htmlspecialchars($book['author']) ?></p>
                    <p class="cat-card-precio">$<?= number_format($book['price'], 2, ',', '.') ?></p>
                    <button class="cat-btn-carrito" type="button" aria-label="Agregar al carrito"></button>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

    </section>

    <?php renderPagination($page, $totalPages, 'Paginación inferior'); ?>

</section>