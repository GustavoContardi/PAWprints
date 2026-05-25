<?php
$errors  = $errors ?? [];
$old     = $old ?? [];
$success = $success ?? false;
?>

<h1>PAWprints</h1>
<h2>Cargar nuevo libro</h2>

<?php if ($success): ?>
    <div class="reserva-exito">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--color-brand)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="reserva-exito-icono">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <p>¡Libro cargado con éxito!<br><span>El nuevo título ya está disponible en el catálogo.</span></p>
        <a href="/catalogue" class="btn-link">Volver al catálogo</a>
    </div>
<?php else: ?>

    <?php if (!empty($errors)): ?>
        <div class="reserva-errores" style="background-color: #ffebee; color: #c62828; border: 1.5px solid #ef9a9a; border-radius: var(--radius); padding: 1rem; margin-bottom: 1.5rem;">
            <p style="font-weight: 600; margin-bottom: 0.5rem;">Por favor, corrija los siguientes errores:</p>
            <ul style="list-style-type: disc; margin-left: 1.5rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/books/new" enctype="multipart/form-data" novalidate id="form-libro-nuevo">
        <fieldset>
            <legend>Cargar nuevo libro</legend>

            <!-- Título -->
            <div class="form-group <?= isset($errors['title']) ? 'has-error' : '' ?>">
                <label for="title">Título *</label>
                <input type="text" name="title" id="title" placeholder="Ej: El Principito" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                <span class="form-error <?= isset($errors['title']) ? 'visible' : '' ?>" id="error-title"><?= htmlspecialchars($errors['title'] ?? '') ?></span>
            </div>

            <!-- Autor -->
            <div class="form-group <?= isset($errors['author']) ? 'has-error' : '' ?>">
                <label for="author">Autor *</label>
                <input type="text" name="author" id="author" placeholder="Ej: Antoine de Saint-Exupéry" value="<?= htmlspecialchars($old['author'] ?? '') ?>">
                <span class="form-error <?= isset($errors['author']) ? 'visible' : '' ?>" id="error-author"><?= htmlspecialchars($errors['author'] ?? '') ?></span>
            </div>

            <!-- Precio -->
            <div class="form-group <?= isset($errors['price']) ? 'has-error' : '' ?>">
                <label for="price">Precio *</label>
                <input type="number" name="price" id="price" min="0" step="0.01" placeholder="Ej: 1500.00" value="<?= htmlspecialchars($old['price'] ?? '') ?>">
                <span class="form-error <?= isset($errors['price']) ? 'visible' : '' ?>" id="error-price"><?= htmlspecialchars($errors['price'] ?? '') ?></span>
            </div>

            <!-- Stock -->
            <div class="form-group <?= isset($errors['stock']) ? 'has-error' : '' ?>">
                <label for="stock">Stock *</label>
                <input type="number" name="stock" id="stock" min="0" step="1" placeholder="Ej: 15" value="<?= htmlspecialchars($old['stock'] ?? '') ?>">
                <span class="form-error <?= isset($errors['stock']) ? 'visible' : '' ?>" id="error-stock"><?= htmlspecialchars($errors['stock'] ?? '') ?></span>
            </div>

            <!-- Descuento -->
            <div class="form-group <?= isset($errors['discount']) ? 'has-error' : '' ?>">
                <label for="discount">Descuento (%)</label>
                <input type="number" name="discount" id="discount" min="0" max="100" step="0.01" placeholder="Ej: 15" value="<?= htmlspecialchars($old['discount'] ?? '') ?>">
                <span class="form-error <?= isset($errors['discount']) ? 'visible' : '' ?>" id="error-discount"><?= htmlspecialchars($errors['discount'] ?? '') ?></span>
            </div>

            <!-- Categoría -->
            <div class="form-group <?= isset($errors['category']) ? 'has-error' : '' ?>">
                <label for="category">Categoría</label>
                <select name="category" id="category">
                    <option value="">-- Seleccionar categoría --</option>
                    <?php 
                    $categories = ['Ficción', 'No ficción', 'Infantil', 'Juvenil', 'Académico', 'Otros'];
                    $selectedCategory = $old['category'] ?? '';
                    foreach ($categories as $cat): ?>
                        <option value="<?= $cat ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="form-error <?= isset($errors['category']) ? 'visible' : '' ?>" id="error-category"><?= htmlspecialchars($errors['category'] ?? '') ?></span>
            </div>

            <!-- Edad Recomendada -->
            <div class="form-group <?= isset($errors['age']) ? 'has-error' : '' ?>">
                <label for="age">Edad recomendada</label>
                <select name="age" id="age">
                    <option value="">-- Seleccionar edad --</option>
                    <?php 
                    $ages = [
                        'Cualquier edad' => 'Cualquier edad',
                        'Infantil (0-8)' => 'Infantil (0-8)',
                        'Juvenil (9-14)' => 'Juvenil (9-14)',
                        'Adolescente (15-17)' => 'Adolescente (15-17)',
                        'Adulto (18+)' => 'Adulto (18+)'
                    ];
                    $selectedAge = $old['age'] ?? '';
                    foreach ($ages as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $selectedAge === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="form-error <?= isset($errors['age']) ? 'visible' : '' ?>" id="error-age"><?= htmlspecialchars($errors['age'] ?? '') ?></span>
            </div>

            <!-- Descripción -->
            <div class="form-group <?= isset($errors['description']) ? 'has-error' : '' ?>">
                <label for="description">Descripción</label>
                <textarea name="description" id="description" placeholder="Sinopsis o descripción del libro..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                <span class="form-error <?= isset($errors['description']) ? 'visible' : '' ?>" id="error-description"><?= htmlspecialchars($errors['description'] ?? '') ?></span>
            </div>

            <!-- Imagen -->
            <div class="form-group <?= isset($errors['image']) ? 'has-error' : '' ?>">
                <label for="image">Imagen de portada</label>
                <input type="file" name="image" id="image" accept="image/png, image/jpeg, image/jpg, image/webp">
                <span class="form-error <?= isset($errors['image']) ? 'visible' : '' ?>" id="error-image"><?= htmlspecialchars($errors['image'] ?? '') ?></span>
            </div>

            <!-- Novedad y Recomendado Checkboxes -->
            <div class="form-group-checkboxes">
                <div class="form-group form-group-inline <?= isset($errors['is_new']) ? 'has-error' : '' ?>">
                    <input type="checkbox" name="is_new" id="is_new" value="1" <?= !empty($old['is_new']) ? 'checked' : '' ?>>
                    <label for="is_new" class="label-inline">Es novedad</label>
                    <span class="form-error <?= isset($errors['is_new']) ? 'visible' : '' ?>" id="error-is_new"><?= htmlspecialchars($errors['is_new'] ?? '') ?></span>
                </div>

                <div class="form-group form-group-inline <?= isset($errors['is_recommended']) ? 'has-error' : '' ?>">
                    <input type="checkbox" name="is_recommended" id="is_recommended" value="1" <?= !empty($old['is_recommended']) ? 'checked' : '' ?>>
                    <label for="is_recommended" class="label-inline">Es recomendado</label>
                    <span class="form-error <?= isset($errors['is_recommended']) ? 'visible' : '' ?>" id="error-is_recommended"><?= htmlspecialchars($errors['is_recommended'] ?? '') ?></span>
                </div>
            </div>

            <button type="submit">Cargar libro</button>
        </fieldset>
    </form>

<?php endif; ?>

<script src="/assets/scripts/libro_nuevo.js?v=<?= time() ?>"></script>
