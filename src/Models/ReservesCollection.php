<?php

namespace Models;

use PDO;

class ReservesCollection
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gets all reserves with optional search filtering, ordered from newest to oldest.
     *
     * @param array $filters
     * @return array{items: array, total: int}
     */
    public function getAll(array $filters = []): array
    {
        $where = " WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (buyer_name ILIKE :search OR email ILIKE :search OR book_title ILIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Calculate total
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM reserves" . $where);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        // Main query
        $query = "SELECT * FROM reserves" . $where . " ORDER BY created_at DESC, id DESC";
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
