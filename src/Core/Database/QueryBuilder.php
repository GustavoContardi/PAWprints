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

        $dsn = "mysql:host=$host;dbname=$dbname";

        return new PDO($dsn, $username, $password);
    }
}