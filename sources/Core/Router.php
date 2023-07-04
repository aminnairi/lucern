<?php

namespace App\Core;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Core\Dependency;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnprocessableEntityException;
use Exception;

final class Router
{
    private array $routes;

    final public function __construct(private Request $request, private JsonResponse $response)
    {
        $this->routes = [];
    }

    final public function matches(string $route, string $path): bool
    {
        $pathSeparatorPattern = "#/#";

        $routeParts = preg_split($pathSeparatorPattern, trim($route, "/"));
        $pathParts = preg_split($pathSeparatorPattern, trim($path, "/"));

        if (count($routeParts) !== count($pathParts)) {
            return false;
        }

        foreach ($routeParts as $routePartIndex => $routePart) {
            $pathPart = $pathParts[$routePartIndex];

            if (str_starts_with($routePart, ":")) {
                continue;
            }

            if ($routePart !== $pathPart) {
                return false;
            }
        }

        return true;
    }

    final public function get(string $path, string $className, string $methodName): void
    {
        $this->routes[] = [
            "method" => "GET",
            "path" => $path,
            "className" => $className,
            "methodName" => $methodName
        ];
    }

    final public function post(string $path, string $className, string $methodName): void
    {
        $this->routes[] = [
            "method" => "POST",
            "path" => $path,
            "className" => $className,
            "methodName" => $methodName
        ];
    }

    final public function delete(string $path, string $className, string $methodName): void
    {
        $this->routes[] = [
            "method" => "DELETE",
            "path" => $path,
            "className" => $className,
            "methodName" => $methodName
        ];
    }

    final public function patch(string $path, string $className, string $methodName): void
    {
        $this->routes[] = [
            "method" => "PATCH",
            "path" => $path,
            "className" => $className,
            "methodName" => $methodName
        ];
    }

    final public function start(): void
    {
        try {
            foreach ($this->routes as $route) {
                if ($route["method"] !== $this->request->getMethod()) {
                    continue;
                }

                if ($this->matches($route["path"], $this->request->getUri())) {
                    $this->request->setRoute($route["path"]);

                    $className = $route["className"];
                    $methodName = $route["methodName"];

                    if (!class_exists($className)) {
                        throw new \Exception("Class $className does not exist");
                    }

                    $controller = Dependency::fromClassName($className, ["App\\Core\\Request" => $this->request]);

                    if (!method_exists($controller, $methodName)) {
                        throw new \Exception("Method $methodName does not exist");
                    }

                    $response = Dependency::fromMethodName($controller, $methodName, ["App\\Core\\Request" => $this->request]);

                    die($response->toString());
                }
            }

            throw new NotFoundException("Route not found");
        } catch (UnprocessableEntityException $exception) {
            $errors = json_decode($exception->getMessage());
            $statusCode = $exception->getCode();

            if ($statusCode === 0 || gettype($statusCode) !== "integer") {
                $statusCode = 422;
            }

            $output = $this->response
                ->withBody(["success" => false, "errors" => $errors])
                ->withStatusCode($statusCode)
                ->toString();

            die($output);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = $exception->getCode();

            if ($statusCode === 0 || gettype($statusCode) !== "integer") {
                $statusCode = 500;
            }

            $output = $this->response
                ->withBody(["success" => false, "error" => $message])
                ->withStatusCode($statusCode)
                ->toString();

            die($output);
        }
    }
}
