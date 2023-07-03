<?php

namespace App\Core;

class Response
{
    public int $statusCode;
    public mixed $body;
    public array $headers;

    final public function setHeaders(): void
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    public function __construct()
    {
        $this->statusCode = 200;
        $this->body = "";
        $this->headers = [];
    }

    final public function withStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    final public function withBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    final public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
}
