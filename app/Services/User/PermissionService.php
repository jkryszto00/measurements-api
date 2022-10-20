<?php

namespace App\Services\User;

use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function createPermission(array $attributes): Permission
    {
        return Permission::create($attributes);
    }

    public function updatePermission(Permission $permission, array $attributes): Permission
    {
        $permission->update($attributes);

        return $permission;
    }

    public function deletepPermission(Permission $permission): void
    {
        $permission->delete();
    }
}
