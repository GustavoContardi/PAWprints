<section class="error-container" style="text-align: center; padding: 100px 20px;">
    <h1 style="font-size: 4rem; color: #e74c3c;"><?= $code ?? 500 ?></h1>
    <h2 style="font-size: 2rem; margin-bottom: 20px;"><?= htmlspecialchars($message ?? 'Error Interno del Servidor') ?></h2>
    <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
        <?= htmlspecialchars($description ?? 'Lo sentimos, ha ocurrido un error inesperado. Por favor, inténtalo de nuevo más tarde.') ?>
    </p>
    <a href="/" class="btn-home" style="display: inline-block; padding: 10px 25px; background: #2c3e50; color: white; text-decoration: none; border-radius: 5px;">
        Volver al Inicio
    </a>
</section>
