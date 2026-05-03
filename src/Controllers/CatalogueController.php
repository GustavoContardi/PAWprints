<?php

namespace Controllers;

use Models\BooksCollection;

class CatalogueController extends Controller
{
    public function index(array $params): void
    {
        $collection = new BooksCollection($this->db);

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, (int)($_GET['per_page'] ?? 12));

        $filters = [
            'category'  => $_GET['category'] ?? null,
            'age'       => $_GET['age'] ?? null,
            'search'    => $_GET['search'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'order'     => $_GET['order'] ?? null,
            'page'      => $page,
            'per_page'  => $perPage,
        ];

        $result = $collection->getAll($filters);

        // Validar que la página solicitada esté en rango
        if ($page > $result['totalPages'] || $page < 1) {
            $this->abort(404, 'Página no encontrada');
        }

        $this->render('catalogue', [
            'title'      => 'Catálogo — PAWprints',
            'styles'     => ['catalogo.css'],
            'books'      => $result['items'],
            'page'       => $result['page'],
            'totalPages' => $result['totalPages'],
            'perPage'    => $result['perPage'],
            'total'      => $result['total'],
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
