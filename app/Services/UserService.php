<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function store(array $credentials, $admin = false)
    {
        try {
            $user = new User();
            $user->email = $credentials['email'];
            $user->password = app('hash')->make($credentials['password']);
            $user->save();

            $admin ? $user->assignRole('admin') : null;
        } catch (\Exception $e) {
            return ['error' => 'Something went wrong!'];
        }

        return [
            'id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            }),
            'permissions' => $user->permissions->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name
                ];
            }),
        ];
    }
}
