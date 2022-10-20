<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;

class UserController extends ApiController
{
    public function __construct(
        private UserService $userService
    ){}

    public function index(): JsonResponse
    {
        try {
            $users = User::all()->load('roles');
            return $this->handleWithDataResponse((array) UserResource::collection($users), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(User $user): JsonResponse
    {
        try {
            $user->load('roles');
            return $this->handleWithDataResponse((array) new UserResource($user), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
                'roles' => 'nullable|array'
            ]);

            if (!$validated['roles']) {
                $user = $this->userService->createUser($validated);
            } else {
                $user = $this->userService->createUserWithRoles($validated);
            }

            $user->load('roles');

            return $this->handleResponse('User created', (array) new UserResource($user), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(User $user, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'roles' => 'nullable|array'
            ]);

            $this->userService->updateUser($user, $validated);
            $user->load('roles');

            return $this->handleResponse('User updated', (array) new UserResource($user), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(User $user): JsonResponse
    {
        try {
            $user->delete();
            return $this->handleWithMessageResponse('User deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
