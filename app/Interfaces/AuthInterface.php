<?php

namespace App\Interfaces;

interface AuthInterface
{
    public function login($data);
    public function register($data);
}
