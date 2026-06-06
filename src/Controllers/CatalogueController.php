<?php

namespace Controllers;

use Models\BooksCollection;

class CatalogueController extends Controller
{
    public function index(array $params): void
    {
        $collection = new BooksCollection($this->db);

        // Fetch all books unfiltered and unpaginated
        $result = $collection->getAll(['paginate' => false]);

        // For initial HTML load (SEO/fallback), slice the first 12 books
        $initialBooks = array_slice($result['items'], 0, 12);
        $total = count($result['items']);
        $perPage = 12;
        $totalPages = (int)ceil($total / $perPage);

        // Build ItemList microdata
        $itemListElements = [];
        foreach ($initialBooks as $index => $book) {
            $itemListElements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Book',
                    'name' => $book['title'],
                    'url' => 'https://pawprints.com/book/' . $book['id'],
                    'author' => ['@type' => 'Person', 'name' => $book['author']],
                    'offers' => ['@type' => 'Offer', 'price' => $book['price'], 'priceCurrency' => 'ARS']
                ]
            ];
        }

        $microdata = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Catálogo de libros — PAWprints',
            'itemListElement' => $itemListElements
        ];

        $this->render('catalogue', [
            'title'      => 'Catálogo — PAWprints',
            'styles'     => ['catalogo.css'],
            'books'      => $initialBooks,
            'allBooks'   => $result['items'],
            'page'       => 1,
            'totalPages' => $totalPages,
            'perPage'    => $perPage,
            'total'      => $total,
            'queryParams' => $_GET,
            'microdata'  => $microdata
        ]);
    }

    public function exportCsv(array $params): void
    {
        $collection = new BooksCollection($this->db);

        $filters = [
            'category'  => $_GET['category'] ?? null,
            'age'       => $_GET['age'] ?? null,
            'search'    => $_GET['search'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'paginate'  => false,
        ];

        $result = $collection->getAll($filters);

        $headers = ['ID', 'Título', 'Autor', 'Precio', 'Stock', 'Categoría', 'Edad'];
        $rows = [];

        foreach ($result['items'] as $book) {
            $rows[] = [
                $book['id'],
                $book['title'],
                $book['author'],
                $book['price'],
                $book['stock'],
                $book['category'],
                $book['age'],
            ];
        }

        \Core\CsvResponse::send('catalogo.csv', $headers, $rows);
    }
}
