<?php

require_once '/var/www/app/src/bootstrap.php';

use Core\Router;

$router = new Router($logger);

$router->get('/',                 'HomeController',     'index');
$router->get('/catalogue/export', 'CatalogueController', 'exportCsv');
$router->get('/catalogue/search', 'CatalogueController', 'search');
$router->get('/catalogue',        'CatalogueController', 'index');
$router->get('/book/{id}',        'BookController',      'show');
$router->get('/about-us',         'PagesController',     'about');
$router->get('/contact',          'PagesController',     'contact');
$router->get('/special',          'PagesController',     'special');
$router->post('/process-contact', 'PagesController',     'processContact');
$router->get('/reserve/{id}',     'ReserveController',   'show');
$router->post('/reserve',         'ReserveController',   'process');

$router->resolve();