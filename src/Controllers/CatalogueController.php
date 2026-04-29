<?php

namespace Controllers;

use Models\BooksCollection;

class CatalogueController extends Controller
{
    public function index(array $params): void
    {
        $collection = new BooksCollection($this->db);
        
        $filters = [
            'category'  => $_GET['category'] ?? null,
            'age'       => $_GET['age'] ?? null,
            'search'    => $_GET['search'] ?? null,
            'max_price' => $_GET['price'] ?? null,
        ];

        $books = $collection->getAll($filters);

        $this->render('catalogue', [
            'title'  => 'Catálogo — PAWprints',
            'styles' => ['catalogo.css'],
            'books'  => $books
        ]);
    }
}
