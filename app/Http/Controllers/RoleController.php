<?php

namespace App\Http\Controllers;

use App\Interfaces\RoleInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(RoleInterface $roleInterface)
    {
        $this->roleInterface = $roleInterface;
    }

    public function index()
    {
        return response()->json([
            'status' => 200,
            'roles' => $this->roleInterface->all()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:roles',
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Utworzono role pomyślnie.',
            'role' => $this->roleInterface->store($request->only('name'))
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 200,
            'role' => $this->roleInterface->get($id)
        ]);
    }

    public function update($id, Request  $request)
    {
        return response()->json([
            'status' => 204,
            'message' => 'Zaktualizowano role pomyślnie.',
            'role' => $this->roleInterface->update($id, $request->only('name', 'permissions'))
        ]);
    }

    public function delete($id)
    {
        $this->roleInterface->delete($id);

        return response()->json([
            'status' => 204,
            'message' => 'Usunięto role pomyślnie.'
        ]);
    }
}
