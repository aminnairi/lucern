<?php

namespace App\Services;

final class TokenService
{
    final public function createToken(): string
    {
        return bin2hex(random_bytes(30));
    }
}
