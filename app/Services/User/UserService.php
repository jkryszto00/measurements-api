<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $attributes): User
    {
        return User::create([
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password'])
        ]);
    }

    public function createUserWithRoles(array $attributes): User
    {
        $user = $this->createUser($attributes);
        $user->assignRole($attributes['roles']);

        return $user;
    }

    public function updateUser(User $user, array $attributes): void
    {
        $user->update($attributes);

        if ($attributes['roles']) {
            $user->syncRoles($attributes['roles']);
        }
    }
}
