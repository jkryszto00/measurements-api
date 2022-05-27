<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Interfaces\UserInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    public function register(Request $request)
    {
        $this->validate($request, [
           'email' => 'required|string|unique:users',
           'password' => 'required|confirmed'
        ]);

        $this->userInterface->store($request->only('email', 'password'));

        return response()->json([
            'status' => 201,
            'message' => 'Użytkownik zarejestrowany pomyślnie.'
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
           'email' => 'required|string|email',
           'password' => 'required|string'
        ]);

        return (new AuthService())->login($request->only('email', 'password'));
    }

    public function profile()
    {
        $user = Auth::user()->load('roles');

        return response()->json(new UserResource($user));

//        return response()->json([
//            'status' => '200',
//            'email' => $user->email,
//            'permissions' => $user->getPermissionsViaRoles()->map(function($permission) {
//                return [
//                    'id' => $permission->id,
//                    'name' => $permission->name
//                ];
//            }),
//        ]);
    }
}
