<?php

namespace App\Models;

final class User
{
    public int $id;
    public string $email;
    public string $password;

    final public function withId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    final public function getId(): int
    {
        return $this->id;
    }

    final public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    final public function getEmail(): string
    {
        return $this->email;
    }

    final public function withPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    final public function getPassword(): string
    {
        return $this->password;
    }

    final public function isValidPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
