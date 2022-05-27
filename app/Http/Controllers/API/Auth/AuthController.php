<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    public function user(): JsonResponse
    {
        return $this->handleResponse('', ['user' => new UserResource(auth()->user())], 200);
    }

    public function refresh()
    {
        return $this->handleResponse('', [
            'access_token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], 200);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return $this->handleResponse('Wylogowano pomyślnie', [], 200);
    }
}
