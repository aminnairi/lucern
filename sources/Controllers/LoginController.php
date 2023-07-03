<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerErrorException;
use App\Repositories\UserRepository;
use App\Services\TokenService;

final class LoginController
{
    final public function __construct(private Request $request, private JsonResponse $response, private UserRepository $userRepository, private TokenService $tokenService) { }

    final public function post(): JsonResponse
    {
        $body = $this->request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        $user = $this->userRepository->getUserByEmail($body["email"]);

        if (!$user) {
            throw new BadRequestException("Invalid credentials");
        }

        $isValidPassword = $user->isValidPassword($body["password"]);

        if (!$isValidPassword) {
            throw new BadRequestException("Invalid credentials");
        }

        $token = $this->tokenService->createToken();

        $success = $this->userRepository->setUserTokenById($user->id, $token);

        if (!$success) {
            throw new InternalServerErrorException("Invalid credentials");
        }

        return $this->response
            ->withField("success", true)
            ->withField("token", $token)
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
