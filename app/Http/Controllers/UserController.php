<?php

namespace App\Http\Controllers;

use App\Interfaces\UserInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function index()
    {
        return response()->json([
            'status' => 200,
            'users' => $this->userInterface->all()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|unique:users',
        ]);

        return response()->json([
            'message' => 'Utworzono użytkownika pomyślnie.',
            'status' => 201,
            'user' => $this->userInterface->store($request->only('email', 'password', 'is_admin'))
        ]);
    }

    public function show($id)
    {
        return response()->json([
           'status' => '200',
           'user' => $this->userInterface->get($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 204,
            'message' => 'Zaktualizowano użytkownika pomyślnie.',
            'user' => $this->userInterface->update($id, $request->only('email', 'roles'))
        ]);
    }

    public function delete($id)
    {
        $this->userInterface->delete($id);

        return response()->json([
            'status' => 204,
            'message' => 'Usunięto użytkownika pomyślnie.'
        ]);
    }
}
