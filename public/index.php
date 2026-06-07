<?php
// ruta relativa
require_once __DIR__ . '/../src/bootstrap.php';

use Core\Router;

$router = new Router($logger);

$router->get('/',                 'HomeController',     'index');
$router->get('/catalogue/export', 'CatalogueController', 'exportCsv');
$router->get('/catalogue',        'CatalogueController', 'index');
$router->get('/book/{id}',        'BookController',      'show');
$router->get('/book/{id}/edit',   'BookController',      'edit');
$router->post('/book/{id}/edit',  'BookController',      'update');
$router->get('/about-us',         'PagesController',     'about');
$router->get('/contact',          'PagesController',     'contact');
$router->get('/special',          'PagesController',     'special');
$router->post('/process-contact', 'PagesController',     'processContact');
$router->get('/reserve/{id}',     'ReserveController',   'show');
$router->post('/reserve',         'ReserveController',   'process');
$router->get('/books/new',        'BookController',      'new');
$router->post('/books/new',       'BookController',      'store');
$router->get('/api/search-book',  'BookController',      'apiSearchBook');
$router->get('/admin/reserves',   'AdminReservesController', 'index');
$router->get('/login',            'AuthController',     'showLogin');
$router->post('/login',           'AuthController',     'login');
$router->post('/logout',          'AuthController',     'logout');

$router->resolve();