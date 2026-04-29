<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'PAWprints') ?></title>
    <link rel="stylesheet" href="/assets/estilos/base.css">
    <link rel="stylesheet" href="/assets/estilos/layout.css">
    <link rel="stylesheet" href="/assets/estilos/components.css">
    <?php if (!empty($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="/assets/estilos/<?= htmlspecialchars($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header>
        <a href="/"><img src="/assets/img/logo_PAWprints.svg" alt="Logo PAWprints"></a>
        <nav>
            <ul>
                <li><a href="/catalogue">Catálogo</a></li>
                <li><a href="/special">Indispensables</a></li>
                <li><a href="/catalogue">Más Vendidos</a></li>
                <li><a href="/about-us">Sobre nosotros</a></li>
                <li><a href="/contact">Contacto</a></li>
                <li><a href="" class="nav-btn">Iniciar sesión</a></li>
                <li><a href="" class="nav-btn">Registrarse</a></li>
                <li><a href="" aria-label="Menú">Menú</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        <h3>Redes Sociales</h3>
        <address class="rs">
            <ul>
                <li><a href="https://www.x.com/pawprints">X</a></li>
                <li><a href="https://www.instagram.com/pawprints">Instagram</a></li>
                <li><a href="https://www.youtube.com/pawprints">YouTube</a></li>
            </ul>
        </address>
        <h3>Contacto</h3>
        <address>
            <p>+54 11 1234 5678</p>
        </address>
        <h3>Dirección</h3>
        <address>
            <p>Argentina, Buenos Aires</p>
            <p>Luján, Humberto Primo 666 (6700)</p>
            <p>2025-2026 PAWprints libros S.A.</p>
        </address>
    </footer>
</body>
</html>