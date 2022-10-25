<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends TestCase
{
    public function testCanRegisterNewUser()
    {
        $user = User::factory()->make();

        $this->json('POST', '/api/v1/auth/register', [
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ])->seeJson([
            'message' => 'User created'
        ])->seeStatusCode(Response::HTTP_CREATED);
    }
}
