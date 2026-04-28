<?php

namespace Controllers;

use Core\View;
use PDO;

abstract class Controller
{
    protected ?PDO $db = null;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }
}
