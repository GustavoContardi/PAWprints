<?php

namespace Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        // Extraemos el array como variables locales
        // ['title' => 'Home'] se convierte en $title = 'Home'
        extract($data);

        // Capturamos el contenido de la view en un buffer
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $content = ob_get_clean();

        // Cargamos el layout con $content ya disponible
        require __DIR__ . '/../Views/layouts/layout.php';
    }
}