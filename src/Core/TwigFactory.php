<?php

namespace Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigFactory
{
    public static function create(): Environment
    {
        $loader = new FilesystemLoader(__DIR__ . '/../Views/');

        $cache = ($_ENV['APP_ENV'] ?? 'development') !== 'production'
            ? false
            : __DIR__ . '/../../storage/twig_cache/';

        $twig = new Environment($loader, [
            'cache' => $cache,
            'auto_reload' => true,
        ]);

        // Global variable for cache-busting
        $twig->addGlobal('asset_version', time());

        // Auth status and details globals
        $twig->addGlobal('is_authenticated', \Core\Session::isAuthenticated());
        $twig->addGlobal('employee_name', \Core\Session::employeeName());

        // Custom csrf_token() function
        $twig->addFunction(new TwigFunction('csrf_token', function (): string {
            return \Core\Session::csrfToken();
        }));

        // Custom asset() function
        $twig->addFunction(new TwigFunction('asset', function (string $path): string {
            return '/assets/' . $path . '?v=' . time();
        }));

        // Custom image_url() function
        $twig->addFunction(new TwigFunction('image_url', function (array $book): string {
            $image = $book['image'] ?? 'placeholder.jpg';
            return (str_starts_with($image, 'http://') || str_starts_with($image, 'https://'))
                ? $image
                : '/assets/img/' . $image;
        }));

        // Custom author_image_url() function
        $twig->addFunction(new TwigFunction('author_image_url', function (array $book): string {
            $image = $book['author_image'] ?? '';
            if (empty($image)) {
                return '/assets/img/avatar_placeholder.jpg';
            }
            return (str_starts_with($image, 'http://') || str_starts_with($image, 'https://'))
                ? $image
                : '/assets/img/' . $image;
        }));

        // Custom price_format filter
        $twig->addFilter(new \Twig\TwigFilter('price_format', function (float $value): string {
            return number_format($value, 2, ',', '.');
        }));

        return $twig;
    }
}
