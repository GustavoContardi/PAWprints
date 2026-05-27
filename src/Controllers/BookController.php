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

    // ── Autenticación simple (temporal, sin roles) ────────────────────────────
    private function requirePassword(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
            $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'changeme';
            if ($_POST['admin_password'] === $adminPassword) {
                $_SESSION['admin_auth'] = true;
                header('Location: /books/new');
                exit;
            } else {
                $this->render('admin_login', [
                    'title' => 'Acceso restringido — PAWprints',
                    'error' => 'Contraseña incorrecta.',
                ]);
                exit;
            }
        }

        if (empty($_SESSION['admin_auth'])) {
            $this->render('admin_login', [
                'title' => 'Acceso restringido — PAWprints',
                'error' => null,
            ]);
            exit;
        }
    }

    public function new(array $params): void
    {
        $this->requirePassword();

        $this->render('libro_nuevo', [
            'title'  => 'Cargar libro — PAWprints',
            'styles' => ['libro_nuevo.css'],
        ]);
    }

    private function uploadToR2(array $file, string $imageName): string|false
    {
        try {
            $s3 = new \Aws\S3\S3Client([
                'version'     => 'latest',
                'region'      => 'auto',
                'endpoint'    => 'https://' . ($_ENV['R2_ACCOUNT_ID'] ?? '') . '.r2.cloudflarestorage.com',
                'credentials' => [
                    'key'    => $_ENV['R2_ACCESS_KEY_ID'] ?? '',
                    'secret' => $_ENV['R2_SECRET_ACCESS_KEY'] ?? '',
                ],
            ]);

            $s3->putObject([
                'Bucket'      => $_ENV['R2_BUCKET'] ?? '',
                'Key'         => $imageName,
                'SourceFile'  => $file['tmp_name'],
                'ContentType' => mime_content_type($file['tmp_name']),
            ]);

            return rtrim($_ENV['R2_PUBLIC_URL'] ?? '', '/') . '/' . $imageName;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function store(array $params): void
    {
        $this->requirePassword();

        // 1. Leer y sanitizar $_POST
        $title          = isset($_POST['title'])          ? trim($_POST['title'])          : '';
        $author         = isset($_POST['author'])         ? trim($_POST['author'])         : '';
        $price          = isset($_POST['price'])          ? trim($_POST['price'])          : '';
        $stock          = isset($_POST['stock'])          ? trim($_POST['stock'])          : '';
        $discount       = isset($_POST['discount'])       ? trim($_POST['discount'])       : '';
        $category       = isset($_POST['category'])       ? trim($_POST['category'])       : '';
        $age            = isset($_POST['age'])            ? trim($_POST['age'])            : '';
        $description    = isset($_POST['description'])    ? trim($_POST['description'])    : '';
        $is_new         = isset($_POST['is_new'])         ? true : false;
        $is_recommended = isset($_POST['is_recommended']) ? true : false;

        $errors = [];

        // 2. Validar server-side
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

        $allowedAges = ['Cualquier edad', 'Infantil (0-8)', 'Juvenil (9-14)', 'Adolescente (15-17)', 'Adulto (18+)'];
        if ($age !== '' && !in_array($age, $allowedAges)) {
            $errors['age'] = 'La edad recomendada seleccionada no es válida.';
        }

        // 3. Manejar upload de imagen
        $imageName = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = 'Error al subir la imagen.';
            } else {
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                $fileType     = mime_content_type($file['tmp_name']);
                $ext          = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($fileType, $allowedTypes) || !in_array($ext, $allowedExts)) {
                    $errors['image'] = 'Formatos permitidos: JPG, JPEG, PNG, WEBP.';
                } elseif ($file['size'] > 2 * 1024 * 1024) {
                    $errors['image'] = 'La imagen no debe superar los 2MB.';
                } else {
                    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                }
            }
        }

        // 4. Si hay errores, volver al formulario
        if (!empty($errors)) {
            $this->render('libro_nuevo', [
                'title'  => 'Cargar libro — PAWprints',
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'old'    => $_POST,
            ]);
            return;
        }

        // 5. Subir imagen a Cloudflare R2
        if ($imageName && isset($file)) {
            $imageUrl = $this->uploadToR2($file, $imageName);
            if ($imageUrl === false) {
                $errors['image'] = 'No se pudo guardar la imagen en el servidor.';
                $this->render('libro_nuevo', [
                    'title'  => 'Cargar libro — PAWprints',
                    'styles' => ['libro_nuevo.css'],
                    'errors' => $errors,
                    'old'    => $_POST,
                ]);
                return;
            }
        }

        // 6. Guardar libro en la DB
        $book = new Book([
            'title'          => $title,
            'author'         => $author,
            'price'          => (float)$price,
            'description'    => $description === '' ? null : $description,
            'stock'          => (int)$stock,
            'image'          => isset($imageUrl) ? $imageUrl : null,
            'category'       => $category === '' ? null : $category,
            'age'            => $age === '' ? null : $age,
            'is_new'         => $is_new,
            'discount'       => $discount !== '' ? (float)$discount : 0.0,
            'is_recommended' => $is_recommended,
        ]);

        if ($book->save($this->db)) {
            $this->render('libro_nuevo', [
                'title'   => 'Libro cargado — PAWprints',
                'styles'  => ['libro_nuevo.css'],
                'success' => true,
            ]);
        } else {
            $errors['general'] = 'Hubo un problema al guardar el libro en la base de datos.';
            $this->render('libro_nuevo', [
                'title'  => 'Cargar libro — PAWprints',
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'old'    => $_POST,
            ]);
        }
    }
}