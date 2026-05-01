<section class="seccion-libro">
    <img class="libro-portada" src="/assets/img/<?= htmlspecialchars($book['image'] ?? 'placeholder.jpg') ?>" alt="Tapa de <?= htmlspecialchars($book['title']) ?>">

    <h2 class="libro-titulo"><?= htmlspecialchars($book['title']) ?></h2>
    <a href="/catalogue?category=<?= urlencode($book['category'] ?? '') ?>" class="libro-genero"><?= htmlspecialchars($book['category'] ?? 'Sin categoría') ?></a>
    <p class="libro-precio">$<?= number_format($book['price'], 2, ',', '.') ?></p>
    <p class="libro-autor"><?= htmlspecialchars($book['author']) ?></p>

    <div class="libro-form">
        <label for="formatoLibro">Formato</label>
        <select id="formatoLibro" name="formato">
            <option value="" disabled selected>Valor</option>
            <option value="pdf">PDF</option>
            <option value="tapa-dura">Tapa dura</option>
            <option value="tapa-blanda">Tapa blanda</option>
        </select>

        <label for="idiomaLibro">Idioma</label>
        <select id="idiomaLibro" name="idioma">
            <option value="" disabled selected>Valor</option>
            <option value="ingles">Inglés</option>
            <option value="espanol">Español</option>
            <option value="frances">Francés</option>
        </select>

        <a href="/reserve/<?= $book['id'] ?>"
           class="btn-comprar"
           <?= $book['stock'] <= 0 ? 'style="pointer-events:none;opacity:0.5"' : '' ?>>
            <?= $book['stock'] > 0 ? 'Reservar Libro' : 'No disponible' ?>
        </a>
    </div>

    <details class="libro-sinopsis">
        <summary>Sinopsis</summary>
        <p><?= htmlspecialchars($book['description'] ?? 'Sin descripción disponible.') ?></p>
    </details>
</section>

<section class="seccion-autor">
    <img class="autor-foto" src="/assets/img/avatar_placeholder.jpg" alt="Foto de <?= htmlspecialchars($book['author']) ?>">
    <h2 class="autor-titulo">Información del autor</h2>
    <h3 class="autor-nombre"><?= htmlspecialchars($book['author']) ?></h3>
    <p class="autor-bio">Información del autor no disponible.</p>
</section>
