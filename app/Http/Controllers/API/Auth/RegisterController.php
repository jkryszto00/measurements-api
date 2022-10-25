<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\ApiController;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends ApiController
{
    public function __invoke(Request $request,  UserService $userService): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()]
            ]);

            $validated['roles'] = ['user'];

            $userService->createUserWithRoles($validated);

            return $this->handleWithMessageResponse('User created', Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
