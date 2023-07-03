<?php

namespace App\Exceptions;

use Exception;

final class UnprocessableEntityException extends Exception
{
    final public function __construct(array $errors)
    {
        parent::__construct(json_encode($errors), 422);
    }
}
