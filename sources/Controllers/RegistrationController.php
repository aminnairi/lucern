<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerErrorException;
use App\Models\User;
use App\Repositories\UserRepository;

final class RegistrationController
{
    final public function __construct(private Request $request, private UserRepository $userRepository, private JsonResponse $response) { }

    final public function post(): JsonResponse
    {
        $body = $this->request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        $foundUser = $this->userRepository->getUserByEmail($body["email"]);

        if ($foundUser) {
            throw new BadRequestException("Email already exists");
        }

        $user = (new User())
            ->withEmail($body["email"])
            ->withPassword($body["password"]);

        $success = $this->userRepository->createUser($user);

        if (!$success) {
            throw new InternalServerErrorException("Failed to register user");
        }

        return $this
            ->response
            ->withField("success", true)
            ->withField("message", "Registred")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
