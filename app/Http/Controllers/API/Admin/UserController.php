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
use Illuminate\Validation\ValidationException;

class UserController extends ApiController
{
    public function __construct(
        private UserService $userService
    ){}

    public function index(): JsonResponse
    {
        try {
            $users = User::with('roles')->get();
            return $this->handleWithDataResponse(UserResource::collection($users), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = User::with('roles')->findOrFail($id);
            return $this->handleWithDataResponse(new UserResource($user), Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return $this->handleResponse('User created', new UserResource($user), Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users',
                'roles' => 'nullable|array'
            ]);

            $user = User::findOrFail($id);
            $this->userService->updateUser($user, $validated);

            return $this->handleResponse('User updated', new UserResource($user), Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->userService->deleteUser($user);

            return $this->handleWithMessageResponse('User deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('User not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
