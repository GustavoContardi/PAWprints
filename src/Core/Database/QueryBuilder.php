<?php

namespace Core\Database;

use PDO;

class ConnectionBuilder
{
    public static function make()
    {
        $host     = 'localhost';
        $dbname   = 'pawprints';
        $username = 'root';
        $password = 'root';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return $pdo;
    }
}