<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAWprints Libros</title>
    <link rel="stylesheet" href="/assets/estilos/base.css">
    <link rel="stylesheet" href="/assets/estilos/layout.css">
    <link rel="stylesheet" href="/assets/estilos/components.css">
    <link rel="stylesheet" href="/assets/estilos/index.css">
</head>
<body>
    <header>
        <a href="/"><img src="/assets/img/logo_PAWprints.svg" alt="Logo PAWprints"></a>
        <nav>
            <ul>
                <li><a href="/catalogo">Catálogo</a></li>
                <li><a href="/indispensables">Indispensables</a></li>
                <li><a href="/catalogo">Más Vendidos</a></li>
                <li><a href="/sobre-nosotros">Sobre nosotros</a></li>
                <li><a href="/contacto">Contacto</a></li>
                <li><a href="" class="nav-btn">Iniciar sesión</a></li>
                <li><a href="" class="nav-btn">Registrarse</a></li>
                <li><a href="" aria-label="Menú">Menú</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h1>PAWprints</h1>
            <h2>Libros</h2>
            <form action="/buscar" method="GET">
                <label for="busqueda">Buscar...</label>
                <input type="search" id="busqueda" placeholder="Buscar...">
                <button type="submit">Buscar</button>
            </form>
        </section>

        <img src="/assets/img/Home_Thumbnnail_PAWprints.png" alt="Foto de la libreria PAWprints">

        <nav class="index-opciones">
            <a href="/catalogo">
                <img src="/assets/img/Home_Button_PAWprints.jpg" alt="Foto de la libreria PAWprints">
                <h3>Catálogo</h3>
            </a>
            <a href="/indispensables">
                <img src="/assets/img/Home_Button_PAWprints.jpg" alt="Foto de la libreria PAWprints">
                <h3>Indispensables</h3>
            </a>
            <a href="/catalogo">
                <img src="/assets/img/Home_Button_PAWprints.jpg" alt="Foto de la libreria PAWprints">
                <h3>Más Vendidos</h3>
            </a>
            <a href="/sobre-nosotros">
                <img src="/assets/img/Home_Button_PAWprints.jpg" alt="Foto de la libreria PAWprints">
                <h3>Acerca de Nosotros</h3>
            </a>
        </nav>
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