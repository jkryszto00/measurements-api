<?php

namespace App\Services\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function authenticate(string $email, string $password): string
    {
        if (!$token = auth()->attempt([
            'email' => $email,
            'password' => $password
        ])) {
            throw new AuthenticationException('Unauthenticated');
        }

        return $token;
    }

    public function getTokenInfo(string $token): array
    {
        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::factory()->getTTL() * 768
        ];
    }
}
