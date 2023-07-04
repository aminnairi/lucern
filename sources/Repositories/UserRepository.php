<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use PDO;

final class UserRepository extends Repository
{
    final public function createUser(User $user, string $confirmationToken): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $createUserQuery = $databaseConnection->prepare("INSERT INTO users(email, password, confirmation_token) VALUES(:email, :password, :confirmation_token)");

        $success = $createUserQuery->execute([
            "email" => htmlspecialchars(strtolower(trim($user->getEmail()))),
            "password" => $user->getPassword(),
            "confirmation_token" => $confirmationToken
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

    final public function setUserAuthenticationTokenById(string $id, string $token): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $setUserTokenByIdQuery = $databaseConnection->prepare("UPDATE users SET authentication_token = :authentication_token WHERE id = :id");

        $success = $setUserTokenByIdQuery->execute([
            "id" => $id,
            "authentication_token" => $token
        ]);

        return $success;
    }

    final public function getUserByAuthenticationToken(string $token): User | null
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getUserByTokenQuery = $databaseConnection->prepare("SELECT * FROM users WHERE authentication_token = :authentication_token");

        $success = $getUserByTokenQuery->execute([
            "authentication_token" => $token
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

    final public function removeUserAuthenticationTokenById(string $id): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $removeTokenByIdQuery = $databaseConnection->prepare("UPDATE users SET authentication_token = NULL WHERE id = :id");

        $success = $removeTokenByIdQuery->execute([
            "id" => $id
        ]);

        return $success;
    }

    final public function getUserByConfirmationToken(string $confirmationToken): User | null
    {
        $databaseConnection = $this->getDatabaseConnection();

        $getUserByConfirmationTokenQuery = $databaseConnection->prepare("SELECT * FROM users WHERE confirmation_token = :confirmation_token");

        $success = $getUserByConfirmationTokenQuery->execute([
            "confirmation_token" => $confirmationToken
        ]);

        if (!$success) {
            return null;
        }

        $getUserByConfirmationTokenQuery->setFetchMode(PDO::FETCH_CLASS, User::class);

        $user = $getUserByConfirmationTokenQuery->fetch();

        if (!$user) {
            return null;
        }

        return $user;
    }

    final public function removeUserConfirmationTokenById(string $id): bool
    {
        $databaseConnection = $this->getDatabaseConnection();

        $removeConfirmationTokenByIdQuery = $databaseConnection->prepare("UPDATE users SET confirmation_token = NULL WHERE id = :id");

        $success = $removeConfirmationTokenByIdQuery->execute([
            "id" => $id
        ]);

        return $success;
    }
}
