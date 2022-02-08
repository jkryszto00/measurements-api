<?php

namespace App\Repositories;

use App\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleInterface
{
    public function all()
    {
        return Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                      'name' => $permission->name
                    ];
                })
            ];
        });
    }

    public function get(int $id)
    {
        $role = Role::findOrFail($id);

        return [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name
                ];
            })
        ];
    }

    public function store(array $data)
    {
        $role = Role::create($data);

        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    }

    public function update(int $id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);

        (isset($data['permissions'])) ? $role->syncPermissions($data['permissions']) : $role->syncPermissions([]);

        return [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'name' => $permission->name
                ];
            })
        ];
    }

    public function delete(int $id)
    {
        return Role::findOrFail($id)->delete();
    }
}
