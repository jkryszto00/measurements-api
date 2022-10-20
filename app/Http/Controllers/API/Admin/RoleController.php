<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\RoleResource;
use App\Services\User\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
            return $this->handleWithDataResponse((array) RoleResource::collection($roles), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Role $role): JsonResponse
    {
        try {
            $role->load('permissions');
            return $this->handleWithDataResponse((array) new RoleResource($role), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $role->load('permissions');
            return $this->handleResponse('Role created', (array) new RoleResource($role), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Role $role, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'name' => 'required|string|unique:roles',
                'permissions' => 'required|array',
                'permissions.*' => 'required|string'
            ]);

            $this->roleService->updateRole($role, $validated);
            $role->load('permissions');

            return $this->handleResponse('Role updated', (array)new RoleResource($role), Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Role $role): JsonResponse
    {
        try {
            $this->roleService->deleteRole($role);
            return $this->handleWithMessageResponse('Role deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
