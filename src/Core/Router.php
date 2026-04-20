<?php

namespace Core;

use Monolog\Logger;

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

        foreach ($this->routes as $route) {
            $params = $this->match($route['path'], $uri);

            if ($route['method'] === $method && $params !== null) {
                $this->logger->info("Ruta resuelta", [
                    'controller' => $route['controller'],
                    'action'     => $route['action'],
                    'params'     => $params,
                ]);

                $controllerClass = 'Controllers\\' . $route['controller'];
                $action          = $route['action'];

                $controller = new $controllerClass();
                $controller->$action($params);
                return;
            }
        }

        $this->logger->warning("Ruta no encontrada", ['method' => $method, 'uri' => $uri]);

        http_response_code(404);
        require __DIR__ . '/Views/404.php';
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