<?php

namespace App\Services\User;

use Spatie\Permission\Models\Role;

class RoleService
{
    public function createRole(array $attributes): Role
    {
        $role = Role::create($attributes);
        $this->syncRoleWithPermissions($role, $attributes['permissions']);

        return $role;
    }

    public function updateRole(Role $role, array $attributes): Role
    {
        $role->update($attributes);
        $this->syncRoleWithPermissions($role, $attributes['permissions']);

        return $role;
    }

    public function syncRoleWithPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }
}
