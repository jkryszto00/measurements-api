<?php

use App\Models\User;
use App\Services\User\UserService;

class UserTest extends TestCase
{
    public function testCanCreateUser()
    {
        $factoryUser = User::factory()->make();
        $createdUser = (new UserService())->createUser(['email' => $factoryUser->email, 'password' => $factoryUser->password]);

        $this->seeInDatabase('users', ['email' => $createdUser->email]);
    }

    public function testCanUpdateUser()
    {
        $userService = new UserService();
        $factoryUser = User::factory()->make();
        $newEmail = User::factory()->make()->email;

        $createdUser = $userService->createUser(['email' => $factoryUser->email, 'password' => $factoryUser->password]);
        $userService->updateUser($createdUser, ['email' => $newEmail]);

        $this->seeInDatabase('users', ['email' => $newEmail]);
    }

    public function testCanDeleteUser()
    {
        $userService = new UserService();

        $factoryUser = User::factory()->make();
        $createdUser = $userService->createUser(['email' => $factoryUser->email, 'password' => $factoryUser->password]);

        $userService->deleteUser($createdUser);

        $this->notSeeInDatabase('users', ['email' => $createdUser->email]);
    }
}
