<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends ApiController
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function user(): JsonResponse
    {
        try {
            return $this->handleWithDataResponse((array) new UserResource($this->authService->user()), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Coś poszło nie tak', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh()
    {
        try {
            return $this->handleWithDataResponse($this->authService->refresh(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Coś poszło nie tak', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return $this->handleWithMessageResponse('Użytkownik wylogowany pomyślnie', Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Coś poszło nie tak', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
