<?php

namespace App\Providers;

use App\Interfaces\MeasurementInterface;
use App\Interfaces\RoleInterface;
use App\Interfaces\UserInterface;
use App\Repositories\MeasurementRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MeasurementInterface::class, MeasurementRepository::class);
        $this->app->bind(RoleInterface::class,RoleRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);
    }
}
