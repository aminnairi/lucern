<?php

namespace App;

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require_once __DIR__ . "/autoload.php";

use App\Core\Router;
use App\Core\Dependency;
use App\Controllers\ArticlesController;
use App\Controllers\RegistrationController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\RegistrationConfirmationController;

// This is where we instanciate the router and all of its dependencies recursively

$router = Dependency::fromClassName(Router::class);

// This is where you can define your routes

$router->post("/registration", RegistrationController::class, "post");
$router->post("/registration/confirmation", RegistrationConfirmationController::class, "post");
$router->post("/login", LoginController::class, "post");
$router->post("/logout", LogoutController::class, "post");
$router->get("/articles", ArticlesController::class, "get");
$router->post("/articles", ArticlesController::class, "post");
$router->delete("/articles/:article", ArticlesController::class, "delete");
$router->patch("/articles/:article", ArticlesController::class, "patch");

// This is when the router will start to match the routes

$router->start();
