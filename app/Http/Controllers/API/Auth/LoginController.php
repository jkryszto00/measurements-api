<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class LoginController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        try {
            if (!$token = auth()->attempt($validated)) throw new UnauthorizedException('Unauthorized');
            $user = auth()->user();

            return $this->handleResponse('Użytkownia zalogowany pomyślnie!', [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ], 200);
        } catch (UnauthorizedException $e) {
            return $this->handleError($e->getMessage(), [], 401);
        } catch (\Exception $e) {
            return $this->handleError('Wystąpił błąd spróbuj ponownie później', [], 400);
        }
    }
}
