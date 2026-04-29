<?php
// Variables disponibles: $book (array|null), $errors (array|null), $old (array|null), $success (bool|null)
$book    = $book ?? null;
$errors  = $errors ?? [];
$old     = $old ?? [];
$success = $success ?? false;
?>

<h1>PAWprints</h1>
<h2>Datos de reserva</h2>

<?php if ($success): ?>
    <div class="reserva-exito">
        <p>Reserva enviada correctamente. Nos pondremos en contacto.</p>
        <?php if ($book): ?>
            <p><a href="/book/<?= $book['id'] ?>">Volver al libro</a></p>
        <?php else: ?>
            <p><a href="/catalogue">Volver al catálogo</a></p>
        <?php endif; ?>
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="reserva-errores">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/reserve">
        <fieldset>
            <legend>Información de pedido</legend>

            <label for="nombre">Nombre entero</label>
            <input type="text" name="nombre" id="nombre"
                   placeholder="eg. Juan Perez"
                   maxlength="255"
                   value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                   required>

            <label for="tel">Teléfono</label>
            <input type="text" name="telefono" id="tel"
                   placeholder="eg. +54 11 1234 5678"
                   maxlength="50"
                   value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                   required>

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email"
                   placeholder="eg. juanperez@yahoo.com"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required>

            <label for="busqueda">Libro a reservar</label>
            <?php if ($book): ?>
                <input type="text" id="busqueda" name="libro"
                       value="<?= htmlspecialchars($book['title']) ?>"
                       readonly required>
                <input type="hidden" name="libro_id" value="<?= $book['id'] ?>">
            <?php else: ?>
                <input type="text" id="busqueda" name="libro"
                       placeholder="Título del libro"
                       value="<?= htmlspecialchars($old['libro'] ?? '') ?>"
                       required>
                <?php if (!empty($old['libro_id'])): ?>
                    <input type="hidden" name="libro_id" value="<?= (int)$old['libro_id'] ?>">
                <?php endif; ?>
            <?php endif; ?>

            <span class="reserva-copias-label">
                <label for="copias">Cantidad de copias</label>
                <output for="copias" id="copias-valor"><?= htmlspecialchars($old['copias'] ?? '1') ?></output>
            </span>
            <input type="range" id="copias" name="copias"
                   min="1" max="100"
                   value="<?= (int)($old['copias'] ?? 1) ?>" step="1"
                   oninput="document.getElementById('copias-valor').value = this.value">

            <button type="submit">Guardar reserva</button>
        </fieldset>
    </form>

<?php endif; ?>