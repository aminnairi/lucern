<?php

spl_autoload_register(function (string $className) {
    $className = str_replace("App\\", "", $className);
    $path = __DIR__ . "/" . str_replace("\\", "/", $className) . ".php";

    if (!file_exists($path)) {
        throw new Exception("Path {$path} not found for class {$className}");
    }

    require_once $path;
});
