<?php

namespace App\Services\Auth;

use App\Models\User;

class RegisterService
{
    public function registerUser(string $email, string $password): void
    {
        $this->storeUser($email, $password);
    }

    public function registerAdmin(string $email, string $password): void
    {
        $user = $this->storeUser($email, $password);
        $user->assignRole('admin');
    }

    private function storeUser(string $email, string $password): User
    {
        return User::create([
            'email' => $email,
            'password' => $this->hashPassword($password)
        ]);
    }

    private function hashPassword(string $blankPassword): string
    {
        return app('hash')->make($blankPassword);
    }
}
