<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InternalServiceErrorException;

final class LogoutController
{
    final public function __construct(private Request $request, private JsonResponse $response, private UserRepository $userRepository, private AuthenticationService $authenticationService) {}

    final public function post(): JsonResponse
    {
        $token = $this->authenticationService->token();

        if (!$token) {
            throw new UnauthorizedException("You must be authenticated to logout.");
        }

        $user = $this->userRepository->getUserByToken($token);

        if (!$user) {
            throw new UnauthorizedException("You must be authenticated to logout.");
        }

        $success = $this->userRepository->removeUserTokenById($user->id);

        if (!$success) {
            throw new InternalServiceErrorException("An error occurred while logging out.");
        }

        return $this->response
            ->withField("success", true)
            ->withField("message", "You have been successfully logged out.")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
