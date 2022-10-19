<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\ApiController;
use App\Http\Resources\PermissionResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends ApiController
{
    public function index(): JsonResponse
    {
        try {
            $permissions = Permission::all();
            return $this->handleWithDataResponse((array) PermissionResource::collection($permissions), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Permission $permission): JsonResponse
    {
        try {
            return $this->handleWithDataResponse((array) new PermissionResource($permission), Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('Permission not found', Response::HTTP_NOT_FOUND);
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

            $permission = Permission::create($validated);

            return $this->handleResponse('Permission created', (array) new PermissionResource($permission), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Permission $permission, Request $request): JsonResponse
    {
        try {
            $validated = $this->validate($request, [
                'name' => 'required|string|unique:roles'
            ]);

            $permission->update($validated);

            return $this->handleResponse('Permission updated', (array)new PermissionResource($permission), Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return $this->handleErrorWithMessage('Permission not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(Permission $permission): JsonResponse
    {
        try {
            $permission->delete();
            return $this->handleWithMessageResponse('Permission deleted', Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return $this->handleErrorWithMessage('Permission not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception) {
            return $this->handleErrorWithMessage('Something went wrong', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
