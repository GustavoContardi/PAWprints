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

    public function new(array $params): void
    {
        $this->render('libro_nuevo', [
            'title'  => 'Cargar libro — PAWprints',
            'styles' => ['libro_nuevo.css'],
        ]);
    }

    public function store(array $params): void
    {
        // 1. Leer y sanitizar $_POST
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $price = isset($_POST['price']) ? trim($_POST['price']) : '';
        $stock = isset($_POST['stock']) ? trim($_POST['stock']) : '';
        $discount = isset($_POST['discount']) ? trim($_POST['discount']) : '';
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $age = isset($_POST['age']) ? trim($_POST['age']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $is_new = isset($_POST['is_new']) ? true : false;
        $is_recommended = isset($_POST['is_recommended']) ? true : false;

        $errors = [];

        // 2. Validar server-side (mismas reglas que el JS)
        if ($title === '') {
            $errors['title'] = 'El título es requerido.';
        } elseif (mb_strlen($title) > 255) {
            $errors['title'] = 'El título no puede superar los 255 caracteres.';
        }

        if ($author === '') {
            $errors['author'] = 'El autor es requerido.';
        } elseif (mb_strlen($author) > 255) {
            $errors['author'] = 'El autor no puede superar los 255 caracteres.';
        }

        if ($price === '') {
            $errors['price'] = 'El precio es requerido.';
        } else {
            $priceFloat = filter_var($price, FILTER_VALIDATE_FLOAT);
            if ($priceFloat === false) {
                $errors['price'] = 'El precio debe ser un número válido.';
            } elseif ($priceFloat < 0) {
                $errors['price'] = 'El precio debe ser mayor o igual a 0.';
            }
        }

        if ($stock === '') {
            $errors['stock'] = 'El stock es requerido.';
        } else {
            $stockInt = filter_var($stock, FILTER_VALIDATE_INT);
            if ($stockInt === false) {
                $errors['stock'] = 'El stock debe ser un número entero.';
            } elseif ($stockInt < 0) {
                $errors['stock'] = 'El stock debe ser mayor o igual a 0.';
            }
        }

        if ($discount !== '') {
            $discountFloat = filter_var($discount, FILTER_VALIDATE_FLOAT);
            if ($discountFloat === false) {
                $errors['discount'] = 'El descuento debe ser un número válido.';
            } elseif ($discountFloat < 0 || $discountFloat > 100) {
                $errors['discount'] = 'El descuento debe estar entre 0 y 100.';
            }
        } else {
            $discountFloat = 0.0;
        }

        // Manejar upload de imagen si existe
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = 'Error al subir la imagen.';
            } else {
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                $fileType = mime_content_type($file['tmp_name']);
                
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (!in_array($fileType, $allowedTypes) || !in_array($ext, $allowedExts)) {
                    $errors['image'] = 'Formatos permitidos: JPG, JPEG, PNG, WEBP.';
                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $errors['image'] = 'La imagen no debe superar los 2MB.';
                } else {
                    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                }
            }
        }

        // 3. Si hay errores: render('libro_nuevo', ['errors' => $errors, 'old' => $_POST, ...])
        if (!empty($errors)) {
            $this->render('libro_nuevo', [
                'title'  => 'Cargar libro — PAWprints',
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'old'    => $_POST
            ]);
            return;
        }

        // 4. Manejar upload de imagen (mover el archivo)
        if ($imageName && isset($file)) {
            $destDir = __DIR__ . '/../../public/assets/img/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            if (!move_uploaded_file($file['tmp_name'], $destDir . $imageName)) {
                $errors['image'] = 'No se pudo guardar la imagen en el servidor.';
                $this->render('libro_nuevo', [
                    'title'  => 'Cargar libro — PAWprints',
                    'styles' => ['libro_nuevo.css'],
                    'errors' => $errors,
                    'old'    => $_POST
                ]);
                return;
            }
        }

        // 5. Instanciar Book con los datos y llamar $book->save($this->db)
        $bookData = [
            'title' => $title,
            'author' => $author,
            'price' => (float)$price,
            'description' => $description === '' ? null : $description,
            'stock' => (int)$stock,
            'image' => $imageName,
            'category' => $category === '' ? null : $category,
            'age' => $age === '' ? null : $age,
            'is_new' => $is_new,
            'discount' => ($discount !== '') ? (float)$discount : 0.0,
            'is_recommended' => $is_recommended
        ];

        $book = new Book($bookData);
        $success = $book->save($this->db);

        // 6. Si ok: render('libro_nuevo', ['success' => true, ...])
        if ($success) {
            $this->render('libro_nuevo', [
                'title'   => 'Libro cargado — PAWprints',
                'styles'  => ['libro_nuevo.css'],
                'success' => true
            ]);
        } else {
            $errors['general'] = 'Hubo un problema al guardar el libro en la base de datos.';
            $this->render('libro_nuevo', [
                'title'  => 'Cargar libro — PAWprints',
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'old'    => $_POST
            ]);
        }
    }
}

