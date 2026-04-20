<?php

namespace Controllers;

use Core\View;

class HomeController
{
    public function index(array $params): void
    {
        View::render('home', [
            'title'  => 'PAWprints — Libros',
            'styles' => ['index.css'],
        ]);
    }
}