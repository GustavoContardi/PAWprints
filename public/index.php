<?php

require_once '/var/www/app/src/bootstrap.php';

use Core\Router;

$router = new Router($logger);

$router->get('/',           'HomeController',     'index');
$router->get('/catalogo',   'CatalogoController', 'index');
$router->get('/libro/{id}', 'LibroController',    'show');

$router->resolve();