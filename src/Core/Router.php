<?php

namespace Core;

use Monolog\Logger;
use Exception;
use Throwable;

class Router
{
    private array $routes = [];
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function get(string $path, string $controller, string $action): void
    {
        $this->add('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->add('POST', $path, $controller, $action);
    }

    public function add(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $this->logger->info("Request entrante", ['method' => $method, 'uri' => $uri]);

        try {
            $pathMatch = false;
            foreach ($this->routes as $route) {
                $params = $this->match($route['path'], $uri);

                if ($params !== null) {
                    $pathMatch = true;
                    if ($route['method'] === $method) {
                        $this->dispatch($route, $params);
                        return;
                    }
                }
            }

            if ($pathMatch) {
                $this->handleError(405, "Método no permitido", "El método HTTP $method no está permitido para esta ruta.");
            } else {
                $this->handleError(404, "Página no encontrada", "Lo sentimos, la página que buscas no existe.");
            }

        } catch (Throwable $e) {
            $this->logger->error("Error crítico en el enrutamiento", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Si Whoops está activo (en dev), dejamos que él lo maneje si no capturamos aquí
            // Pero para producción queremos una página 500 limpia
            if (getenv('APP_ENV') === 'production') {
                $this->handleError(500, "Error Interno del Servidor", "Ha ocurrido un error técnico. Nuestro equipo ha sido notificado.");
            } else {
                throw $e; // En dev, dejamos que Whoops muestre el error detallado
            }
        }
    }

    private function dispatch(array $route, array $params): void
    {
        $this->logger->info("Ruta resuelta", [
            'controller' => $route['controller'],
            'action'     => $route['action'],
            'params'     => $params,
        ]);

        $controllerClass = 'Controllers\\' . $route['controller'];
        $action          = $route['action'];

        if (!class_exists($controllerClass)) {
            throw new Exception("El controlador $controllerClass no existe.");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            throw new Exception("La acción $action no existe en el controlador $controllerClass.");
        }

        $controller->$action($params);
    }

    private function handleError(int $code, string $message, string $description): void
    {
        $this->logger->warning("Error de enrutamiento", ['code' => $code, 'message' => $message]);
        
        http_response_code($code);
        
        // Usamos View::render si está disponible, o un require directo como fallback
        if (class_exists('Core\View')) {
            View::render('error', [
                'title' => "$code - $message",
                'code' => $code,
                'message' => $message,
                'description' => $description
            ]);
        } else {
            require __DIR__ . '/../Views/error.php';
        }
    }

    private function match(string $routePath, string $uri): ?array
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            return array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }
}