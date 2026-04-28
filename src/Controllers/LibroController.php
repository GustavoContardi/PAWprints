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
            $this->abort(404, 'Libro no encontrado');
            return;
        }

        $this->render('libro', [
            'title'  => "{$book->getTitulo()} — PAWprints",
            'styles' => ['libro.css'],
            'libro'  => $book->toArray()
        ]);
    }
}
