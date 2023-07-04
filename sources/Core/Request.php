<?php

namespace App\Core;

use App\Exceptions\UnprocessableEntityException;

final class Request
{
    public string $method;
    public string $uri;
    public string $route;
    public array $headers;
    public array $body;

    final public function __construct()
    {
        $requestUri = $_SERVER["REQUEST_URI"];
        $parsedRequestUri = parse_url($requestUri);

        $this->headers = getallheaders();
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->uri = $parsedRequestUri["path"];
        $this->route = "";
        $this->body = json_decode(file_get_contents("php://input"), true) ?? [];
    }

    final public function getMethod(): string
    {
        return $this->method;
    }

    final public function getUri(): string
    {
        return $this->uri;
    }

    final public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    final public function header(string $name): string | null
    {
        return $this->headers[$name] ?? null;
    }

    final public function parameter(string $name): string | null
    {
        $pathSeparatorPattern = "#/#";

        $routeParts = preg_split($pathSeparatorPattern, trim($this->route, "/"));
        $pathParts = preg_split($pathSeparatorPattern, trim($this->uri, "/"));

        foreach ($routeParts as $routePartIndex => $routePart) {
            if (str_starts_with($routePart, ":")) {
                $parameterName = substr($routePart, 1);

                if ($parameterName === $name) {
                    return $pathParts[$routePartIndex];
                }
            }
        }

        return null;
    }

    final public function body(string $key): string | null
    {
        return $this->body[$key] ?? null;
    }

    final public function validate(array $fields): array
    {
        $errors = [];

        foreach ($fields as $fieldName => $fieldRules) {
            $fieldRules = explode("|", $fieldRules);

            foreach ($fieldRules as $fieldRule) {
                if ($fieldRule === "required") {
                    if ($this->body($fieldName) === null) {
                        $errors[$fieldName][] = "Field $fieldName is required";
                    }
                }

                if (str_starts_with($fieldRule, "min:")) {
                    $minLength = intval(substr($fieldRule, strlen("min:")));

                    if (strlen($this->body($fieldName)) < $minLength) {
                        $errors[$fieldName][] = "Field $fieldName must be at least $minLength characters long";
                    }
                }

                if (str_starts_with($fieldRule, "max:")) {
                    $maxLength = intval(substr($fieldRule, strlen("max:")));

                    if (strlen($this->body($fieldName)) > $maxLength) {
                        $errors[$fieldName][] = "Field $fieldName must be at most $maxLength characters long";
                    }
                }

                if (str_starts_with($fieldRule, "email")) {
                    if (!filter_var($this->body($fieldName), FILTER_VALIDATE_EMAIL)) {
                        $errors[$fieldName][] = "Field $fieldName must be a valid email address";
                    }
                }
            }
        }

        if (count($errors) === 0) {
            return $this->body;
        }

        throw new UnprocessableEntityException($errors);
    }

    final public function query(string $key): string | null
    {
        return $_GET[$key] ?? null;
    }
}
