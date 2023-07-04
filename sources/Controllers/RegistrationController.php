<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Responses\JsonResponse;
use App\Exceptions\BadRequestException;
use App\Exceptions\InternalServerErrorException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\EmailService;
use App\Services\TokenService;

final class RegistrationController
{
    final public function __construct(private Request $request, private UserRepository $userRepository, private JsonResponse $response, private TokenService $tokenService, private EmailService $emailService)
    {
    }

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

        $confirmationToken = $this->tokenService->createToken();

        $success = $this->userRepository->createUser($user, $confirmationToken);

        if (!$success) {
            throw new InternalServerErrorException("Failed to register user");
        }

        $success = $this
            ->emailService
            ->withReceiver($user->email)
            ->withSender("Your Friendly App")
            ->withSubject("Confirm registration")
            ->withBody("You can confirm you registration using this token: $confirmationToken")
            ->send();

        if (!$success) {
            throw new InternalServerErrorException("Failed to send the confirmation email for email {$user->email}");
        }

        return $this
            ->response
            ->withField("success", true)
            ->withField("message", "Registred")
            ->withHeader("X-Server", "IIS/10.0")
            ->withStatusCode(200);
    }
}
