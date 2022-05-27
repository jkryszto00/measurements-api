<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return $this->handleResponse('Użytkownik zarejestrowany pomyślnie', [], 201);
    }
}
