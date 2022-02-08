<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name
            ];
        }), 200);
    }
}
