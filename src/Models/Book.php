<?php

namespace Models;

use PDO;

class Book
{
    private ?int $id;
    private string $title;
    private string $author;
    private float $price;
    private ?string $description;
    private int $stock;
    private ?string $image;
    private ?string $category;
    private ?string $age;
    private bool $is_new;
    private float $discount;
    private bool $is_recommended;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? $data['titulo'] ?? '';
        $this->author = $data['author'] ?? $data['autor'] ?? '';
        $this->price = (float)($data['price'] ?? $data['precio'] ?? 0);
        $this->description = $data['description'] ?? $data['descripcion'] ?? null;
        $this->stock = (int)($data['stock'] ?? 0);
        $this->image = $data['image'] ?? $data['imagen'] ?? null;
        $this->category = $data['category'] ?? $data['categoria'] ?? null;
        $this->age = $data['age'] ?? $data['edad'] ?? null;
        $this->is_new = (bool)($data['is_new'] ?? $data['es_novedad'] ?? false);
        $this->discount = (float)($data['discount'] ?? $data['descuento'] ?? 0);
        $this->is_recommended = (bool)($data['is_recommended'] ?? $data['es_recomendado'] ?? false);
    }

    public static function find(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public function save(PDO $pdo): bool
    {
        if ($this->id) {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, price = ?, description = ?, stock = ?, image = ?, category = ?, age = ?, is_new = ?, discount = ?, is_recommended = ? WHERE id = ?");
            return $stmt->execute([
                $this->title, $this->author, $this->price, $this->description, $this->stock, $this->image,
                $this->category, $this->age, $this->is_new, $this->discount, $this->is_recommended,
                $this->id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO books (title, author, price, description, stock, image, category, age, is_new, discount, is_recommended) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $this->title, $this->author, $this->price, $this->description, $this->stock, $this->image,
                $this->category, $this->age, $this->is_new, $this->discount, $this->is_recommended
            ]);
            if ($result) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return $result;
        }
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getAuthor(): string { return $this->author; }
    public function getPrice(): float { return $this->price; }
    public function getDescription(): ?string { return $this->description; }
    public function getStock(): int { return $this->stock; }
    public function getImage(): ?string { return $this->image; }
    public function getCategory(): ?string { return $this->category; }
    public function getAge(): ?string { return $this->age; }
    public function isNew(): bool { return $this->is_new; }
    public function getDiscount(): float { return $this->discount; }
    public function isRecommended(): bool { return $this->is_recommended; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'price' => $this->price,
            'description' => $this->description,
            'stock' => $this->stock,
            'image' => $this->image,
            'category' => $this->category,
            'age' => $this->age,
            'is_new' => $this->is_new,
            'discount' => $this->discount,
            'is_recommended' => $this->is_recommended
        ];
    }
}
