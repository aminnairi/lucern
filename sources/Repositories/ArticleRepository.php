<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Article;
use PDO;

final class ArticleRepository extends Repository
{
    final public function getArticles(): array
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getArticlesQuery = $databaseConnection->query("SELECT * FROM articles");

        $getArticlesQuery->setFetchMode(PDO::FETCH_CLASS, Article::class);

        $articles = $getArticlesQuery->fetchAll();

        return $articles;
    }

    final public function createArticle(array $body): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $createArticleQuery = $databaseConnection->prepare("INSERT INTO articles (description) VALUES (:description)");

        $success = $createArticleQuery->execute([
            "description" => $body["description"],
        ]);

        return $success;
    }

    final public function deleteArticle(string $id): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $deleteArticleQuery = $databaseConnection->prepare("DELETE FROM articles WHERE id = :id");

        $success = $deleteArticleQuery->execute([
            "id" => $id,
        ]);

        return $success;
    }

    final public function getArticleById(string $id): Article | null
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getArticleByIdQuery = $databaseConnection->prepare("SELECT * FROM articles WHERE id = :id");

        $getArticleByIdQuery->execute([
            "id" => $id,
        ]);

        $getArticleByIdQuery->setFetchMode(PDO::FETCH_CLASS, Article::class);

        $article = $getArticleByIdQuery->fetch();

        if (!$article) {
            return null;
        }

        return $article;
    }

    final public function updateArticle(string $id, array $body): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $updateArticleQuery = $databaseConnection->prepare("UPDATE articles SET description = :description WHERE id = :id");

        $success = $updateArticleQuery->execute([
            "id" => $id,
            "description" => $body["description"],
        ]);

        return $success;
    }
}
