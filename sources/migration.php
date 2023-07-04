<?php

namespace App;

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require_once __DIR__ . "/autoload.php";

use App\Repositories\Repository;
use Exception;

try {
    $repository = new Repository();

    $databaseConnection = $repository->getDatabaseConnection();

    $databaseConnection->query("DROP TABLE IF EXISTS users;");

    $databaseConnection->query("
            CREATE TABLE users(
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(50) UNIQUE NOT NULL,
            password CHAR(60) NOT NULL,
            authentication_token CHAR(60) NULL,
            confirmation_token CHAR(60) NULL
        )
    ");

    $databaseConnection->query("DROP TABLE IF EXISTS articles;");

    $databaseConnection->query("
        CREATE TABLE articles(
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            description TEXT NOT NULL
        )
    ");

    echo "Datamodel migrated successfully." . PHP_EOL;
} catch (Exception $exception) {
    echo "Unable to migrate the datamodel." . PHP_EOL;
    die($exception->getMessage());
}
