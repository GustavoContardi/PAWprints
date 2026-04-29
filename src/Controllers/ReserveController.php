<?php

namespace Controllers;

use Models\Book;

class ReserveController extends Controller
{
    /**
     * GET /reserve/{id} — Muestra el formulario de reserva para un libro.
     */
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
        }

        if ($book->getStock() <= 0) {
            $this->abort(400, 'Libro sin stock');
        }

        $this->render('reserve', [
            'title'  => "Reservar: {$book->getTitle()} — PAWprints",
            'styles' => ['reservaLibro.css'],
            'book'   => $book->toArray(),
        ]);
    }

    /**
     * POST /reserve — Procesa el formulario de reserva.
     *
     * Método HTTP: POST
     * Encoding: application/x-www-form-urlencoded (default de <form method="post">)
     */
    public function process(array $params): void
    {
        global $logger;

        // ── Leer datos del formulario ────────────────────────────────────────
        $nombre   = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $libro    = trim($_POST['libro'] ?? '');
        $libroId  = (int)($_POST['libro_id'] ?? 0);
        $copias   = (int)($_POST['copias'] ?? 0);

        $old = [
            'nombre'   => $nombre,
            'telefono' => $telefono,
            'email'    => $email,
            'libro'    => $libro,
            'libro_id' => $libroId,
            'copias'   => $copias,
        ];

        // ── Validaciones backend ─────────────────────────────────────────────
        // Consistentes con los atributos required, type="email", min/max del HTML.
        $errors = [];

        if ($nombre === '' || mb_strlen($nombre) > 255) {
            $errors[] = 'El nombre es obligatorio y no debe superar los 255 caracteres.';
        }

        if ($telefono === '' || mb_strlen($telefono) > 50) {
            $errors[] = 'El teléfono es obligatorio y no debe superar los 50 caracteres.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Debe ingresar un correo electrónico válido.';
        }

        if ($libro === '') {
            $errors[] = 'Debe indicar el libro a reservar.';
        }

        if ($copias < 1 || $copias > 100) {
            $errors[] = 'La cantidad de copias debe ser un número entre 1 y 100.';
        }

        // Recuperar datos del libro para repoblar la vista
        $bookData = null;
        if ($libroId > 0) {
            $bookObj = Book::find($this->db, $libroId);
            if ($bookObj) {
                $bookData = $bookObj->toArray();
            }
        }

        // ── Si hay errores, re-renderizar con errores ────────────────────────
        if (!empty($errors)) {
            $this->render('reserve', [
                'title'  => 'Reserva — PAWprints',
                'styles' => ['reservaLibro.css'],
                'book'   => $bookData,
                'errors' => $errors,
                'old'    => $old,
            ]);
            return;
        }

        // ── Envío de email ───────────────────────────────────────────────────
        $to      = $_ENV['MAIL_RESERVA'] ?? 'reservas@pawprints.com';
        $subject = "Nueva reserva: " . $libro;
        $body    = "Nombre: $nombre\nTeléfono: $telefono\nEmail: $email\nLibro: $libro\nCopias: $copias";
        $headers = "From: noreply@pawprints.com\r\nReply-To: $email";

        $enviado = @mail($to, $subject, $body, $headers);

        if ($enviado) {
            $logger->info('Reserva enviada por email', [
                'to'     => $to,
                'libro'  => $libro,
                'copias' => $copias,
                'email'  => $email,
            ]);
        } else {
            $logger->error('Error al enviar email de reserva', [
                'to'     => $to,
                'libro'  => $libro,
                'copias' => $copias,
                'email'  => $email,
            ]);
        }

        // ── Renderizar vista con mensaje de éxito ────────────────────────────
        $this->render('reserve', [
            'title'   => 'Reserva confirmada — PAWprints',
            'styles'  => ['reservaLibro.css'],
            'book'    => $bookData,
            'success' => true,
        ]);
    }
}
