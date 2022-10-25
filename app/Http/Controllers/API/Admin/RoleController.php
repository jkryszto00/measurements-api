<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\RoleResource;
use App\Services\User\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class RoleController extends ApiController
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $roles = Role::all();
            return $this->handleWithDataResponse(RoleResource::collection($roles), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return $this->handleWithDataResponse(new RoleResource($role), Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'name' => 'required|string|unique:roles',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string'
            ]);

            $role = $this->roleService->createRole($validated);
            return $this->handleResponse('Role created', new RoleResource($role), Response::HTTP_CREATED);
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
                'name' => 'required|string|unique:roles',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string'
            ]);

            $role = Role::with('permissions')->findOrFail($id);
            $this->roleService->updateRole($role, $validated);

            return $this->handleResponse('Role updated', new RoleResource($role), Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->handleError($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            $this->roleService->deleteRole($role);

            return $this->handleWithMessageResponse('Role deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
