<?php

use App\Models\User;
use Illuminate\Http\Response;

class LoginTest extends TestCase
{
    public function testCanLogin()
    {
        $user = User::factory()->make();

        $this->json('POST', '/api/v1/auth/register', [
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $user->password
        ])->seeJson([
            'message' => 'User login successful'
        ])->seeJsonStructure([
            'message',
            'data' => [
                'token',
                'token_type',
                'expires_in'
            ]
        ])->seeStatusCode(Response::HTTP_OK);
    }
}
