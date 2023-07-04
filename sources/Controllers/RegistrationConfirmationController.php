<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerErrorException;
use App\Repositories\UserRepository;

final class RegistrationConfirmationController
{
    final public function __construct(private Request $request, private JsonResponse $response, private UserRepository $userRepository)
    {
    }

    final public function post(): JsonResponse
    {
        $body = $this->request->validate([
            "confirmation_token" => "required"
        ]);

        $confirmationToken = $body["confirmation_token"];

        $user = $this->userRepository->getUserByConfirmationToken($confirmationToken);

        if (!$user) {
            throw new BadRequestException("Invalid token provided");
        }

        $success = $this->userRepository->removeUserConfirmationTokenById($user->id);

        if (!$success) {
            throw new InternalServerErrorException("Failed to confirm registration");
        }

        return $this
            ->response
            ->withField("success", true)
            ->withField("message", "Registration confirmed")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
