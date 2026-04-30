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
     * Gets all books with optional filters and pagination.
     *
     * Supported filters:
     *   - category (string|array)
     *   - age (string|array)
     *   - search (string) — ILIKE on title/author
     *   - max_price (numeric)
     *   - page (int, default 1)
     *   - per_page (int, default 10)
     *   - paginate (bool, default true) — set to false to skip pagination and return all rows
     *
     * @return array{items: array, total: int, page: int, perPage: int, totalPages: int}
     */
    public function getAll(array $filters = []): array
    {
        $where = " WHERE 1=1";
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
                $where .= " AND category IN (" . implode(',', $placeholders) . ")";
            } else {
                $where .= " AND category = :category";
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
                $where .= " AND age IN (" . implode(',', $placeholders) . ")";
            } else {
                $where .= " AND age = :age";
                $params['age'] = $filters['age'];
            }
        }

        if (!empty($filters['search'])) {
            $where .= " AND (title ILIKE :search OR author ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where .= " AND price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        // ── COUNT total ──────────────────────────────────────────────────────
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM books" . $where);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // ── Pagination parameters ────────────────────────────────────────────
        $paginate = $filters['paginate'] ?? true;
        $page     = max(1, (int)($filters['page'] ?? 1));
        $perPage  = max(1, (int)($filters['per_page'] ?? 10));

        $totalPages = $paginate ? (int)ceil($total / $perPage) : 1;
        // Ensure at least 1 total page even with 0 results
        $totalPages = max(1, $totalPages);

        // ── Main query ───────────────────────────────────────────────────────
        $orderClause = " ORDER BY id ASC";
        if (!empty($filters['order'])) {
            switch ($filters['order']) {
                case 'new':
                    $orderClause = " ORDER BY is_new DESC, id DESC";
                    break;
                case 'popular':
                    $orderClause = " ORDER BY sales DESC, id ASC";
                    break;
                case 'price':
                    $orderClause = " ORDER BY price ASC, id ASC";
                    break;
            }
        }
        $query = "SELECT * FROM books" . $where . $orderClause;

        if ($paginate) {
            $offset = ($page - 1) * $perPage;
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($paginate) {
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return [
            'items'      => $stmt->fetchAll(),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => $totalPages,
        ];
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
