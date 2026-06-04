<?php

namespace Models;

use PDO;

class Reserve
{
    private ?int $id;
    private ?int $book_id;
    private ?string $book_title;
    private string $buyer_name;
    private string $phone;
    private string $email;
    private int $copies;
    private ?string $created_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->book_id = isset($data['book_id']) && $data['book_id'] > 0 ? (int)$data['book_id'] : null;
        $this->book_title = $data['book_title'] ?? $data['libro'] ?? null;
        $this->buyer_name = $data['buyer_name'] ?? $data['nombre'] ?? '';
        $this->phone = $data['phone'] ?? $data['telefono'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->copies = (int)($data['copies'] ?? $data['copias'] ?? 1);
        $this->created_at = $data['created_at'] ?? null;
    }

    public static function find(PDO $pdo, int $id): ?self
    {
        $stmt = $pdo->prepare("SELECT * FROM reserves WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public function save(PDO $pdo): bool
    {
        $stmt = $pdo->prepare(
            "INSERT INTO reserves (book_id, book_title, buyer_name, phone, email, copies)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $result = $stmt->execute([
            $this->book_id ?? null,
            $this->book_title,
            $this->buyer_name,
            $this->phone,
            $this->email,
            $this->copies,
        ]);
        if ($result) {
            $this->id = (int)$pdo->lastInsertId();
            $this->created_at = date('Y-m-d H:i:s');
        }
        return $result;
    }

    public function getId(): ?int { return $this->id; }
    public function getBookId(): ?int { return $this->book_id; }
    public function getBookTitle(): ?string { return $this->book_title; }
    public function getBuyerName(): string { return $this->buyer_name; }
    public function getPhone(): string { return $this->phone; }
    public function getEmail(): string { return $this->email; }
    public function getCopies(): int { return $this->copies; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'book_id' => $this->book_id,
            'book_title' => $this->book_title,
            'buyer_name' => $this->buyer_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'copies' => $this->copies,
            'created_at' => $this->created_at,
        ];
    }
}
