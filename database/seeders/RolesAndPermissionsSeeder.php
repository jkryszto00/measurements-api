<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = ['admin', 'user'];
        $permissions = [
            'show measurements',
            'create measurements',
            'update measurements',
            'delete measurements',
            'merge measurements',
            'export measurements',
            'show users',
            'create users',
            'update users',
            'delete users',
            'show roles',
            'create roles',
            'update roles',
            'delete roles',
            'show permissions'
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }

        $admin = Role::where('name', 'admin')->firstOrFail();
        $admin->givePermissionTo(Permission::all());
    }
}
