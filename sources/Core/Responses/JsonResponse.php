<?php

namespace App\Core\Responses;

use App\Core\Response;

final class JsonResponse extends Response
{
    final public function __construct()
    {
        parent::__construct();
        $this->headers["Content-Type"] = "application/json";
        $this->body = [];
    }

    final public function withField(string $name, mixed $value): self
    {
        $this->body[$name] = $value;
        return $this;
    }

    final public function toString()
    {
        http_response_code($this->statusCode);

        $this->setHeaders();

        return json_encode($this->body);
    }
}
