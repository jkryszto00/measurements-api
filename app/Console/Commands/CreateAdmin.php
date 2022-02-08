<?php

namespace App\Console\Commands;

use App\Services\AuthService;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'make:admin-user';
    protected $description = 'Create an admin user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $email = $this->ask('What is your email?');
        $password = $this->secret('Set password for account:');

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if ((new AuthService())->register($credentials, true)) {
            return $this->info('User created successful!');
        }

        return $this->error('Something went wrong. Try again');
    }
}
