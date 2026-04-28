<?php

namespace Controllers;

use Models\Book;

class LibroController extends Controller
{
    public function show(array $params): void
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            header('Location: /catalogo');
            exit;
        }

        $book = Book::find($this->db, (int)$id);

        if (!$book) {
            http_response_code(404);
            $this->render('404', ['title' => 'Libro no encontrado']);
            return;
        }

        $this->render('libro', [
            'title'  => "{$book->getTitulo()} — PAWprints",
            'styles' => ['libro.css'],
            'libro'  => $book->toArray()
        ]);
    }
}
