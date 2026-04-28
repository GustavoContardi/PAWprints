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
     * Obtiene todos los libros con filtros opcionales
     */
    public function getAll(array $filters = []): array
    {
        $query = "SELECT * FROM libros WHERE 1=1";
        $params = [];

        // Filtro por categorías (soporta array)
        if (!empty($filters['categoria'])) {
            if (is_array($filters['categoria'])) {
                $placeholders = [];
                foreach ($filters['categoria'] as $i => $cat) {
                    $key = "cat_$i";
                    $placeholders[] = ":$key";
                    $params[$key] = $cat;
                }
                $query .= " AND categoria IN (" . implode(',', $placeholders) . ")";
            } else {
                $query .= " AND categoria = :categoria";
                $params['categoria'] = $filters['categoria'];
            }
        }

        // Filtro por edad (soporta array)
        if (!empty($filters['edad'])) {
            if (is_array($filters['edad'])) {
                $placeholders = [];
                foreach ($filters['edad'] as $i => $e) {
                    $key = "edad_$i";
                    $placeholders[] = ":$key";
                    $params[$key] = $e;
                }
                $query .= " AND edad IN (" . implode(',', $placeholders) . ")";
            } else {
                $query .= " AND edad = :edad";
                $params['edad'] = $filters['edad'];
            }
        }

        if (!empty($filters['busqueda'])) {
            $query .= " AND (titulo ILIKE :busqueda OR autor ILIKE :busqueda)";
            $params['busqueda'] = '%' . $filters['busqueda'] . '%';
        }

        if (isset($filters['precio_max']) && $filters['precio_max'] !== '') {
            $query .= " AND precio <= :precio_max";
            $params['precio_max'] = (float)$filters['precio_max'];
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getNovedades(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE es_novedad = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDescuentos(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE descuento > 0 LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecomendados(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE es_recomendado = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
