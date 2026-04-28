<?php

namespace Controllers;

use Core\View;
use PDO;
use Exception;

abstract class Controller
{
    protected ?PDO $db = null;

    public function __construct()
    {
        global $pdo;
        if (!$pdo) {
            throw new Exception("No se pudo establecer la conexión con la base de datos.");
        }
        $this->db = $pdo;
    }

    protected function render(string $view, array $data = []): void
    {
        try {
            View::render($view, $data);
        } catch (Exception $e) {
            global $logger;
            if ($logger) {
                $logger->error("Error al renderizar la vista: " . $view, ['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    protected function abort(int $code = 404, string $message = 'No encontrado'): void
    {
        http_response_code($code);
        View::render('error', [
            'title' => "$code - $message",
            'code' => $code,
            'message' => $message,
            'description' => 'La operación solicitada no pudo completarse.'
        ]);
        exit;
    }
}
