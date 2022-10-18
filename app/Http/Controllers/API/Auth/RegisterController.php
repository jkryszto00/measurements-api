<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\ApiController;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;

class RegisterController extends ApiController
{
    public function __invoke(Request $request, RegisterService $registerService): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()]
            ]);

            $registerService->registerUser($validated['email'], $validated['password']);

            return $this->handleWithMessageResponse('Użytkownik zarejestrowany pomyślnie', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Coś poszło nie tak', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
