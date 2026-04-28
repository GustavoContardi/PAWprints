<?php

require_once '/var/www/app/src/bootstrap.php';

use Core\Router;

$router = new Router($logger);

$router->get('/',                 'HomeController',     'index');
$router->get('/catalogo',         'CatalogoController', 'index');
$router->get('/libro/{id}',       'LibroController',    'show');
$router->get('/sobre-nosotros',   'PagesController',    'about');
$router->get('/contacto',         'PagesController',    'contact');
$router->get('/indispensables',   'PagesController',    'indispensables');

$router->resolve();