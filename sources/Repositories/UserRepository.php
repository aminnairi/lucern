<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use PDO;

final class UserRepository extends Repository
{
    final public function createUser(User $user): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $createUserQuery = $databaseConnection->prepare("INSERT INTO users(email, password) VALUES(:email, :password)");

        $success = $createUserQuery->execute([
            "email" => htmlspecialchars($user->getEmail()),
            "password" => $user->getPassword()
        ]);

        return $success;
    }

    final public function getUsers(): array
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getUsersQuery = $databaseConnection->prepare("SELECT * FROM users");

        $success = $getUsersQuery->execute();

        if (!$success) {
            return [];
        }

        $users = $getUsersQuery->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }

    final public function getUserByEmail(string $email): User | null
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getUserByEmailQuery = $databaseConnection->prepare("SELECT * FROM users WHERE email = :email");

        $success = $getUserByEmailQuery->execute([
            "email" => htmlspecialchars($email)
        ]);

        if (!$success) {
            return null;
        }

        $getUserByEmailQuery->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $getUserByEmailQuery->fetch();

        if ($user) {
            return $user;
        }

        return null;
    }

    final public function setUserTokenById(string $id, string $token): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $setUserTokenByIdQuery = $databaseConnection->prepare("UPDATE users SET token = :token WHERE id = :id");

        $success = $setUserTokenByIdQuery->execute([
            "id" => $id,
            "token" => $token
        ]);

        return $success;
    }

    final public function getUserByToken(string $token): User | null
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getUserByTokenQuery = $databaseConnection->prepare("SELECT * FROM users WHERE token = :token");

        $success = $getUserByTokenQuery->execute([
            "token" => $token
        ]);

        if (!$success) {
            return null;
        }

        $getUserByTokenQuery->setFetchMode(PDO::FETCH_CLASS, User::class);

        $user = $getUserByTokenQuery->fetch();

        if (!$user) {
            return null;
        }

        return $user;
    }

    final public function removeUserTokenById(string $id): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $removeTokenByIdQuery = $databaseConnection->prepare("UPDATE users SET token = NULL WHERE id = :id");

        $success = $removeTokenByIdQuery->execute([
            "id" => $id
        ]);

        return $success;
    }
}
