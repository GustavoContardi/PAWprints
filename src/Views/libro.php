<article class="libro-detalle">
    <div class="libro-imagen">
        <img src="/assets/img/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
    </div>
    
    <div class="libro-datos">
        <h1><?= htmlspecialchars($libro['titulo']) ?></h1>
        <p class="autor">por <strong><?= htmlspecialchars($libro['autor']) ?></strong></p>
        
        <div class="libro-meta">
            <span class="categoria"><?= htmlspecialchars($libro['categoria']) ?></span>
            <span class="stock"><?= $libro['stock'] > 0 ? 'En stock' : 'Sin stock' ?> (<?= $libro['stock'] ?> unidades)</span>
        </div>
        
        <div class="descripcion">
            <h2>Descripción</h2>
            <p><?= htmlspecialchars($libro['descripcion']) ?></p>
        </div>
        
        <div class="compra">
            <p class="precio">$<?= number_format($libro['precio'], 2, ',', '.') ?></p>
            <button class="btn-comprar" <?= $libro['stock'] <= 0 ? 'disabled' : '' ?>>
                <?= $libro['stock'] > 0 ? 'Reservar Libro' : 'No disponible' ?>
            </button>
        </div>
    </div>
</article>

<section class="relacionados">
    <h2>También te puede interesar</h2>
    <!-- Aquí irían libros relacionados -->
</section>
