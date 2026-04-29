<?php

namespace Controllers;

use Models\Book;

class BookController extends Controller
{
    public function show(array $params): void
    {
        $id = $params['id'] ?? null;

        if (!$id) {
            header('Location: /catalogue');
            exit;
        }

        $book = Book::find($this->db, (int)$id);

        if (!$book) {
            $this->abort(404, 'Libro no encontrado');
            return;
        }

        $this->render('libro', [
            'title'  => "{$book->getTitle()} — PAWprints",
            'styles' => ['libro.css'],
            'book'   => $book->toArray()
        ]);
    }
}
