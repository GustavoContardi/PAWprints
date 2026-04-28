<?php

namespace Controllers;

use Models\BooksCollection;

class CatalogoController extends Controller
{
    public function index(array $params): void
    {
        $collection = new BooksCollection($this->db);
        
        $filters = [
            'categoria' => $_GET['categoria'] ?? null,
            'edad'      => $_GET['edad'] ?? null,
            'busqueda'  => $_GET['busqueda'] ?? null,
            'precio_max' => $_GET['precio'] ?? null,
        ];

        $books = $collection->getAll($filters);

        $this->render('catalogue', [
            'title'  => 'Catálogo — PAWprints',
            'styles' => ['catalogo.css'],
            'books'  => $books
        ]);
    }
}
