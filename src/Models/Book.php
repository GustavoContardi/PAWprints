<?php

namespace Models;

use PDO;

class Book
{
    private ?int $id;
    private string $titulo;
    private string $autor;
    private float $precio;
    private ?string $descripcion;
    private int $stock;
    private ?string $imagen;
    private ?string $categoria;
    private ?string $edad;
    private bool $es_novedad;
    private float $descuento;
    private bool $es_recomendado;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->titulo = $data['titulo'] ?? '';
        $this->autor = $data['autor'] ?? '';
        $this->precio = (float)($data['precio'] ?? 0);
        $this->descripcion = $data['descripcion'] ?? null;
        $this->stock = (int)($data['stock'] ?? 0);
        $this->imagen = $data['imagen'] ?? null;
        $this->categoria = $data['categoria'] ?? null;
        $this->edad = $data['edad'] ?? null;
        $this->es_novedad = (bool)($data['es_novedad'] ?? false);
        $this->descuento = (float)($data['descuento'] ?? 0);
        $this->es_recomendado = (bool)($data['es_recomendado'] ?? false);
    }

    public static function find(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare("SELECT * FROM libros WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public function save(PDO $pdo): bool
    {
        if ($this->id) {
            $stmt = $pdo->prepare("UPDATE libros SET titulo = ?, autor = ?, precio = ?, descripcion = ?, stock = ?, imagen = ?, categoria = ?, edad = ?, es_novedad = ?, descuento = ?, es_recomendado = ? WHERE id = ?");
            return $stmt->execute([
                $this->titulo, $this->autor, $this->precio, $this->descripcion, $this->stock, $this->imagen,
                $this->categoria, $this->edad, $this->es_novedad ? 1 : 0, $this->descuento, $this->es_recomendado ? 1 : 0,
                $this->id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO libros (titulo, autor, precio, descripcion, stock, imagen, categoria, edad, es_novedad, descuento, es_recomendado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $this->titulo, $this->autor, $this->precio, $this->descripcion, $this->stock, $this->imagen,
                $this->categoria, $this->edad, $this->es_novedad ? 1 : 0, $this->descuento, $this->es_recomendado ? 1 : 0
            ]);
            if ($result) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return $result;
        }
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getAutor(): string { return $this->autor; }
    public function getPrecio(): float { return $this->precio; }
    public function getDescripcion(): ?string { return $this->descripcion; }
    public function getStock(): int { return $this->stock; }
    public function getImagen(): ?string { return $this->imagen; }
    public function getCategoria(): ?string { return $this->categoria; }
    public function getEdad(): ?string { return $this->edad; }
    public function isNovedad(): bool { return $this->es_novedad; }
    public function getDescuento(): float { return $this->descuento; }
    public function isRecomendado(): bool { return $this->es_recomendado; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'precio' => $this->precio,
            'descripcion' => $this->descripcion,
            'stock' => $this->stock,
            'imagen' => $this->imagen,
            'categoria' => $this->categoria,
            'edad' => $this->edad,
            'es_novedad' => $this->es_novedad,
            'descuento' => $this->descuento,
            'es_recomendado' => $this->es_recomendado,
            // Soporte para nombres en inglés usados en algunas vistas
            'title' => $this->titulo,
            'image' => $this->imagen,
            'price' => $this->precio
        ];
    }
}
