<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\ApiController;
use App\Services\Auth\LoginService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    public function __invoke(Request $request, LoginService $loginService): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            $token = $loginService->authenticate($validated['email'], $validated['password']);
            $data = $loginService->getTokenInfo($token);

            return $this->handleResponse('User login successful', $data, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (AuthenticationException $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
