<?php

namespace App\Exceptions;

use Exception;

final class InternalServerErrorException extends Exception
{
    final public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}
