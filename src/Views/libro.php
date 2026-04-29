<article class="libro-detalle">
    <div class="libro-imagen">
        <img src="/assets/img/<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
    </div>
    
    <div class="libro-datos">
        <h1><?= htmlspecialchars($book['title']) ?></h1>
        <p class="autor">por <strong><?= htmlspecialchars($book['author']) ?></strong></p>
        
        <div class="libro-meta">
            <span class="categoria"><?= htmlspecialchars($book['category']) ?></span>
            <span class="stock"><?= $book['stock'] > 0 ? 'En stock' : 'Sin stock' ?> (<?= $book['stock'] ?> unidades)</span>
        </div>
        
        <div class="descripcion">
            <h2>Descripción</h2>
            <p><?= htmlspecialchars($book['description']) ?></p>
        </div>
        
        <div class="compra">
            <p class="precio">$<?= number_format($book['price'], 2, ',', '.') ?></p>
            <button class="btn-comprar" <?= $book['stock'] <= 0 ? 'disabled' : '' ?>>
                <?= $book['stock'] > 0 ? 'Reservar Libro' : 'No disponible' ?>
            </button>
        </div>
    </div>
</article>

<section class="relacionados">
    <h2>También te puede interesar</h2>
    <!-- Aquí irían libros relacionados -->
</section>
