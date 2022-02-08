<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use App\Models\User;

class UserRepository implements UserInterface
{

    public function all()
    {
        return User::with('roles')->get()->map(function ($user) {
           return [
               'id' => $user->id,
               'email' => $user->email,
               'roles' => $user->roles->map(function ($role) {
                   return [
                       'id' => $role->id,
                       'name' => $role->name
                   ];
               })
           ];
        });
    }

    public function get(int $id)
    {
        $user = User::findOrFail($id);

        return [
            'id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            })
        ];
    }

    public function store(array $data)
    {
        $data['password'] =  app('hash')->make($data['password']);

        $user = User::create($data);

        if (isset($data['is_admin']) and !empty($data['is_admin']) and $data['is_admin'] == true) {
            $user->syncRoles(['admin']);
        }

        return [
          'id' => $user->id,
          'email' => $user->email,
          'roles' => $user->roles->map(function($role) {
              return [
                  'id' => $role->id,
                  'name' => $role->name
              ];
          })
        ];
    }

    public function update(int $id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);

        (isset($data['roles'])) ? $user->syncRoles($data['roles']) : $user->syncRoles([]);

        return [
            'id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            })
        ];
    }

    public function delete(int $id)
    {
        return User::findOrFail($id)->delete();
    }
}
