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

        if (!empty($filters['categoria'])) {
            $query .= " AND categoria = :categoria";
            $params['categoria'] = $filters['categoria'];
        }

        if (!empty($filters['edad'])) {
            $query .= " AND edad = :edad";
            $params['edad'] = $filters['edad'];
        }

        if (!empty($filters['busqueda'])) {
            $query .= " AND (titulo ILIKE :busqueda OR autor ILIKE :busqueda)";
            $params['busqueda'] = '%' . $filters['busqueda'] . '%';
        }

        if (isset($filters['precio_max'])) {
            $query .= " AND precio <= :precio_max";
            $params['precio_max'] = $filters['precio_max'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Obtiene libros marcados como novedades
     */
    public function getNovedades(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE es_novedad = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene libros con descuento
     */
    public function getDescuentos(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE descuento > 0 LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene libros recomendados
     */
    public function getRecomendados(int $limit = 10): array
    {
        $stmt = $this->db->prepare("SELECT * FROM libros WHERE es_recomendado = TRUE LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
