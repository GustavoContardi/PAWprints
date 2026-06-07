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

    protected function requireAdmin(string $redirectTo): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
            $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'changeme';
            if ($_POST['admin_password'] === $adminPassword) {
                $_SESSION['admin_auth'] = true;
                header("Location: " . $redirectTo);
                exit;
            } else {
                $this->render('admin_login', [
                    'title' => 'Acceso restringido — PAWprints',
                    'error' => 'Contraseña incorrecta.',
                ]);
                exit;
            }
        }

        if (empty($_SESSION['admin_auth'])) {
            $this->render('admin_login', [
                'title' => 'Acceso restringido — PAWprints',
                'error' => null,
            ]);
            exit;
        }
    }
}

