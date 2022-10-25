<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthService
{
    public function user(): Authenticatable
    {
        return auth()->user();
    }

    public function refresh(): array
    {
        return [
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    public function logout(): void
    {
        auth()->logout();
    }
}
