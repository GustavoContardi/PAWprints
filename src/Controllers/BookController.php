<?php

namespace Controllers;

use Models\Book;
use Services\OpenLibraryService;

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

        $bookArray = $book->toArray();

        // Build Book microdata
        $microdata = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $bookArray['title'],
            'author' => [
                '@type' => 'Person',
                'name' => $bookArray['author']
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $bookArray['price'],
                'priceCurrency' => 'ARS',
                'availability' => $bookArray['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
            ],
            'image' => (str_starts_with($bookArray['image'] ?? '', 'http') ? $bookArray['image'] : '/assets/img/' . ($bookArray['image'] ?? 'placeholder.jpg')),
            'genre' => $bookArray['category'],
            'description' => $bookArray['description']
        ];

        $this->render('libro', [
            'title'  => "{$book->getTitle()} — PAWprints",
            'styles' => ['libro.css'],
            'book'   => $bookArray,
            'microdata' => $microdata
        ]);
    }


    public function apiSearchBook(array $params): void
    {
        $query = trim($_GET['q'] ?? '');
        if ($query === '') {
            http_response_code(400);
            echo json_encode(['error' => 'El parámetro "q" es requerido.']);
            return;
        }

        header('Content-Type: application/json');
        $results = OpenLibraryService::searchByTitle($query);
        if ($results === null) {
            echo json_encode(['results' => []]);
            return;
        }

        echo json_encode(['results' => $results]);
    }

    public function new(array $params): void
    {
        $this->requireAuth();

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
        $this->requireAuth();

        if (!\Core\Session::validateCsrf($_POST['csrf_token'] ?? null)) {
            $this->render('libro_nuevo', [
                'title'  => 'Cargar libro — PAWprints',
                'styles' => ['libro_nuevo.css'],
                'errors' => ['csrf' => 'Sesión expirada, intentá de nuevo.'],
                'old'    => $_POST,
            ]);
            return;
        }

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

        $allowedCategories = ['ciencia-ficcion', 'romance', 'aventura', 'fantasia', 'misterio', 'historia', 'no-ficcion', 'otros'];
        if ($category === '') {
            $errors['category'] = 'La categoría es requerida.';
        } elseif (!in_array($category, $allowedCategories)) {
            $errors['category'] = 'La categoría seleccionada no es válida.';
        }

        $allowedAges = ['infantil', 'juvenil', 'adulto'];
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

        // 5b. Si no se subió imagen, intentar recuperar portada desde Open Library
        if (!$imageName) {
            $coverId = isset($_POST['cover_id']) ? (int)$_POST['cover_id'] : null;
            $destDir = __DIR__ . '/../../public/assets/img/libros/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if ($coverId > 0) {
                $downloaded = OpenLibraryService::downloadCover($coverId, $destDir);
                if ($downloaded) {
                    $imageName = $downloaded;
                }
            }

            if (!$imageName && $title !== '') {
                $searchQuery = $title . ($author !== '' ? ' ' . $author : '');
                $downloaded = OpenLibraryService::searchAndDownloadFirstCover($searchQuery, $destDir);
                if ($downloaded) {
                    $imageName = $downloaded;
                }
            }
        }

        // 6. Guardar libro en la DB
        $book = new Book([
            'title'          => $title,
            'author'         => $author,
            'price'          => (float)$price,
            'description'    => $description === '' ? null : $description,
            'stock'          => (int)$stock,
            'image'          => isset($imageUrl) ? $imageUrl : (isset($imageName) ? 'libros/' . $imageName : null),
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

    public function edit(array $params): void
    {
        $this->requireAuth();

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

        $this->render('libro_editar', [
            'title' => "Editar {$book->getTitle()} — PAWprints",
            'styles' => ['libro_nuevo.css'],
            'book' => $book->toArray(),
        ]);
    }

    public function update(array $params): void
    {
        $this->requireAuth();

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

        if (!\Core\Session::validateCsrf($_POST['csrf_token'] ?? null)) {
            $this->render('libro_editar', [
                'title'  => "Editar {$book->getTitle()} — PAWprints",
                'styles' => ['libro_nuevo.css'],
                'errors' => ['csrf' => 'Sesión expirada, intentá de nuevo.'],
                'book'   => $book->toArray(),
                'old'    => $_POST,
            ]);
            return;
        }

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
        $author_bio     = isset($_POST['author_bio'])     ? trim($_POST['author_bio'])     : '';

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

        $allowedCategories = ['ciencia-ficcion', 'romance', 'aventura', 'fantasia', 'misterio', 'historia', 'no-ficcion', 'otros'];
        if ($category === '') {
            $errors['category'] = 'La categoría es requerida.';
        } elseif (!in_array($category, $allowedCategories)) {
            $errors['category'] = 'La categoría seleccionada no es válida.';
        }

        $allowedAges = ['infantil', 'juvenil', 'adulto'];
        if ($age !== '' && !in_array($age, $allowedAges)) {
            $errors['age'] = 'La edad recomendada seleccionada no es válida.';
        }

        // 3. Manejar upload de imagen de portada
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

        // 3b. Manejar upload de imagen de autor
        $authorImageName = null;
        if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $authorFile = $_FILES['author_image'];
            if ($authorFile['error'] !== UPLOAD_ERR_OK) {
                $errors['author_image'] = 'Error al subir la imagen del autor.';
            } else {
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                $fileType     = mime_content_type($authorFile['tmp_name']);
                $ext          = strtolower(pathinfo($authorFile['name'], PATHINFO_EXTENSION));
                $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($fileType, $allowedTypes) || !in_array($ext, $allowedExts)) {
                    $errors['author_image'] = 'Formatos permitidos: JPG, JPEG, PNG, WEBP.';
                } elseif ($authorFile['size'] > 2 * 1024 * 1024) {
                    $errors['author_image'] = 'La imagen del autor no debe superar los 2MB.';
                } else {
                    $authorImageName = 'author_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                }
            }
        }

        // 4. Si hay errores, volver al formulario
        if (!empty($errors)) {
            $this->render('libro_editar', [
                'title'  => "Editar {$book->getTitle()} — PAWprints",
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'book'   => $book->toArray(),
                'old'    => $_POST,
            ]);
            return;
        }

        // 5. Subir imagen de portada a Cloudflare R2 o descargar de Open Library
        $imageToSave = $book->getImage();
        if ($imageName && isset($file)) {
            $imageUrl = $this->uploadToR2($file, $imageName);
            if ($imageUrl === false) {
                $errors['image'] = 'No se pudo guardar la imagen en el servidor.';
                $this->render('libro_editar', [
                    'title'  => "Editar {$book->getTitle()} — PAWprints",
                    'styles' => ['libro_nuevo.css'],
                    'errors' => $errors,
                    'book'   => $book->toArray(),
                    'old'    => $_POST,
                ]);
                return;
            }
            $imageToSave = $imageUrl;
        } else {
            $coverId = isset($_POST['cover_id']) ? (int)$_POST['cover_id'] : null;
            if ($coverId > 0) {
                $destDir = __DIR__ . '/../../public/assets/img/libros/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                $downloaded = OpenLibraryService::downloadCover($coverId, $destDir);
                if ($downloaded) {
                    $imageToSave = 'libros/' . $downloaded;
                }
            }
        }

        // 5b. Subir imagen del autor a Cloudflare R2
        $authorImageToSave = $book->getAuthorImage();
        if ($authorImageName && isset($authorFile)) {
            $authorImageUrl = $this->uploadToR2($authorFile, $authorImageName);
            if ($authorImageUrl === false) {
                $errors['author_image'] = 'No se pudo guardar la imagen del autor en el servidor.';
                $this->render('libro_editar', [
                    'title'  => "Editar {$book->getTitle()} — PAWprints",
                    'styles' => ['libro_nuevo.css'],
                    'errors' => $errors,
                    'book'   => $book->toArray(),
                    'old'    => $_POST,
                ]);
                return;
            }
            $authorImageToSave = $authorImageUrl;
        }

        // 6. Guardar libro actualizado en la DB
        $updatedBook = new Book([
            'id'             => $book->getId(),
            'title'          => $title,
            'author'         => $author,
            'price'          => (float)$price,
            'description'    => $description === '' ? null : $description,
            'stock'          => (int)$stock,
            'image'          => $imageToSave,
            'category'       => $category === '' ? null : $category,
            'age'            => $age === '' ? null : $age,
            'is_new'         => $is_new,
            'discount'       => $discount !== '' ? (float)$discount : 0.0,
            'is_recommended' => $is_recommended,
            'author_bio'     => $author_bio === '' ? null : $author_bio,
            'author_image'   => $authorImageToSave,
        ]);

        if ($updatedBook->save($this->db)) {
            header("Location: /book/" . $book->getId());
            exit;
        } else {
            $errors['general'] = 'Hubo un problema al guardar los cambios del libro en la base de datos.';
            $this->render('libro_editar', [
                'title'  => "Editar {$book->getTitle()} — PAWprints",
                'styles' => ['libro_nuevo.css'],
                'errors' => $errors,
                'book'   => $book->toArray(),
                'old'    => $_POST,
            ]);
        }
    }
}