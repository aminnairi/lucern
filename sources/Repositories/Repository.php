<?php

namespace App\Repositories;

use PDO;

class Repository
{
    private PDO $databaseConnection;

    final public function __construct()
    {
        $user = $_ENV["DATABASE_USER"];
        $password = $_ENV["DATABASE_PASSWORD"];
        $name = $_ENV["DATABASE_NAME"];

        $this->databaseConnection = new PDO("mysql:host=mariadb;dbname=$name", $user, $password);
    }

    final public function getDatabaseConnection(): PDO
    {
        return $this->databaseConnection;
    }
}
