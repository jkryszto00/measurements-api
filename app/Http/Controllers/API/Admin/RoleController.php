<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\RoleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

class RoleController extends ApiController
{
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
                'name' => 'required|string|unique:roles'
            ]);

            $role = Role::create($validated);

            return $this->handleResponse('Role created', (array) new RoleResource($role), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Role $role, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'name' => 'required|string|unique:roles'
            ]);

            $role->update($validated);

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
            $role->delete();
            return $this->handleWithMessageResponse('Role deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Role not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
