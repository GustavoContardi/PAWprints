<?php

namespace Models;

use PDO;

class BooksCollection
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gets all books with optional filters
     */
    public function getAll(array $filters = []): array
    {
        $query = "SELECT * FROM books WHERE 1=1";
        $params = [];

        // Filter by categories (supports array)
        if (!empty($filters['category'])) {
            if (is_array($filters['category'])) {
                $placeholders = [];
                foreach ($filters['category'] as $i => $cat) {
                    $key = "cat_$i";
                    $placeholders[] = ":$key";
                    $params[$key] = $cat;
                }
                $query .= " AND category IN (" . implode(',', $placeholders) . ")";
            } else {
                $query .= " AND category = :category";
                $params['category'] = $filters['category'];
            }
        }

        // Filter by age (supports array)
        if (!empty($filters['age'])) {
            if (is_array($filters['age'])) {
                $placeholders = [];
                foreach ($filters['age'] as $i => $e) {
                    $key = "age_$i";
                    $placeholders[] = ":$key";
                    $params[$key] = $e;
                }
                $query .= " AND age IN (" . implode(',', $placeholders) . ")";
            } else {
                $query .= " AND age = :age";
                $params['age'] = $filters['age'];
            }
        }

        if (!empty($filters['search'])) {
            $query .= " AND (title ILIKE :search OR author ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $query .= " AND price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getNew(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE is_new = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSales(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE discount > 0 LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecommended(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE is_recommended = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
