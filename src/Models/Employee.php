<?php

namespace Models;

use PDO;

class Employee
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $password_hash;
    private string $created_at;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
        $this->created_at = $data['created_at'] ?? '';
    }

    public static function findByEmail(PDO $pdo, string $email): ?self
    {
        $normalizedEmail = strtolower(trim($email));
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ?");
        $stmt->execute([$normalizedEmail]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password_hash);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
