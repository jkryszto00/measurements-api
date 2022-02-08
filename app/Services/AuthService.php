<?php

namespace App\Services;

use App\Interfaces\AuthInterface;
use App\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthService implements AuthInterface
{
    public function login($data)
    {
        if (! $token = Auth::attempt($data)) {
            return response()->json(['message' => 'Błędny login lub hasło']);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'message' => 'Użytkownik zalogowany pomyślnie',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL()  * 768
        ]);
    }

    public function register($data, $isAdmin = false)
    {
        ($isAdmin) ? $data['is_admin'] = true : null;

        return (new UserRepository())->store($data);
    }
}
