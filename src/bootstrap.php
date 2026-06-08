<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;


// ── 1. Variables de entorno ──────────────────────────────────────────────────
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// ── 1.1. Security Headers for XSS Prevention ─────────────────────────────────
// Content Security Policy to restrict sources of scripts, styles, etc.
$csp = "default-src 'self'; "
     . "script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
     . "style-src 'self' 'unsafe-inline'; "
     . "img-src 'self' data: https:; "
     . "font-src 'self' data:; "
     . "connect-src 'self'; "
     . "frame-ancestors 'none';";
header("Content-Security-Policy: $csp");

// X-Content-Type-Options to prevent MIME sniffing
header("X-Content-Type-Options: nosniff");

// X-Frame-Options to prevent clickjacking
header("X-Frame-Options: DENY");

// X-XSS-Protection for older browsers
header("X-XSS-Protection: 1; mode=block");

// ── 1.2. Secure Session Cookie Settings ───────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);

\Core\Session::start();

// ── 2. Whoops (solo en desarrollo) ──────────────────────────────────────────
// En producción no se registra para evitar exponer stack traces, rutas de
// archivos, variables de entorno y credenciales al usuario.
if (($_ENV['APP_ENV'] ?? 'development') !== 'production') {
    $whoops = new Run();
    $whoops->pushHandler(new PrettyPageHandler());
    $whoops->register();
}

// ── 3. Logger ────────────────────────────────────────────────────────────────
$logger = new Logger('pawprints');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../log/app.log', Logger::DEBUG));

// ── 4. Base de datos ─────────────────────────────────────────────────────────
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'],
        $_ENV['DB_PORT'],
        $_ENV['DB_NAME']
    );

    class SafePDOStatement extends PDOStatement {
        protected function __construct() {}

        public function execute(?array $params = null): bool {
            if ($params !== null) {
                foreach ($params as $key => $val) {
                    if (is_bool($val)) {
                        $params[$key] = $val ? 'true' : 'false';
                    }
                }
            }
            return parent::execute($params);
        }
    }

    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['SafePDOStatement', []]);

    $logger->info('Conexión a la base de datos establecida');
} catch (PDOException $e) {
    $logger->critical('Error de conexión a la base de datos', ['error' => $e->getMessage()]);
    if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
        http_response_code(500);
        echo '<h1>Error del servidor</h1><p>No se pudo establecer conexión. Intente más tarde.</p>';
        exit;
    }
    throw $e;
}

if (isset($router)) {
    $router->get('/books/new', 'BookController', 'new');
    $router->post('/books/new', 'BookController', 'store');
}