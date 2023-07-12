<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\Request;
use App\Models\User;

final class AuthenticationService
{
    public function __construct(private UserRepository $userRepository, private Request $request)
    {
    }

    final public function authenticated(): User
    {
        $authorizationHeader = $this->request->header("Authorization");

        if (!$authorizationHeader) {
            return false;
        }

        $authorizationHeaderParts = explode(" ", $authorizationHeader);

        if (count($authorizationHeaderParts) !== 2) {
            return false;
        }

        $authorizationType = $authorizationHeaderParts[0];
        $authorizationToken = $authorizationHeaderParts[1];

        if ($authorizationType !== "Bearer") {
            return false;
        }

        if (!$authorizationToken) {
            return false;
        }

        $user = $this->userRepository->getUserByAuthenticationToken($authorizationToken);

        if (!$user) {
            return false;
        }

        return $user;
    }

    final public function token(): string | null
    {
        $authorizationHeader = $this->request->header("Authorization");

        if (!$authorizationHeader) {
            return null;
        }

        $authorizationHeaderParts = explode(" ", $authorizationHeader);

        if (count($authorizationHeaderParts) !== 2) {
            return null;
        }

        $authorizationType = $authorizationHeaderParts[0];
        $authorizationToken = $authorizationHeaderParts[1];

        if ($authorizationType !== "Bearer") {
            return null;
        }

        if (!$authorizationToken) {
            return null;
        }

        return $authorizationToken;
    }
}
