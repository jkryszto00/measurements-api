<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\ApiController;
use App\Services\Auth\LoginService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends ApiController
{
    public function __invoke(Request $request, LoginService $loginService): JsonResponse
    {
        $validated = $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        try {
            $token = $loginService->authenticate($validated['email'], $validated['password']);
            $data = $loginService->getTokenInfo($token);

            return $this->handleResponse('Użytkownik zalogowany pomyślnie', $data, Response::HTTP_OK);
        } catch (AuthenticationException $e) {
            return $this->handleErrorWithMessage('Zły email lub hasło', Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Coś poszło nie tak', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
