<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group([
    'prefix' => 'api/v1',
], function () use ($router) {
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('login', 'API\Auth\LoginController');
        $router->post('register', 'API\Auth\RegisterController');
        $router->get('user', 'API\Auth\AuthController@user');
        $router->post('refresh', 'API\Auth\AuthController@refresh');
        $router->post('logout', 'API\Auth\AuthController@logout');
    });

    $router->group(['prefix' => 'measurements'], function () use ($router) {
        $router->group(['middleware' => 'can:merge measurements'], function () use ($router) {
            $router->get('export', 'API\Export\ExportController');
        });

        $router->group(['prefix' => 'can:export measurements'], function () use ($router) {
            $router->post('merge', 'API\Measurement\MergeMeasurementController');
        });

        $router->group(['middleware' => 'can:crud measurements'], function () use ($router) {
            $router->get('autosuggestion', 'API\Measurement\AutosuggestionMeasurementController');
            $router->get('', 'API\Measurement\MeasurementController@index');
            $router->post('', 'API\Measurement\MeasurementController@store');
            $router->get('{id}', 'API\Measurement\MeasurementController@show');
            $router->patch('{id}', 'API\Measurement\MeasurementController@update');
            $router->delete('{id}', 'API\Measurement\MeasurementController@delete');
        });
    });

    $router->group(['prefix' => 'admin', 'middleware' => ['can:crud users|crud roles|crud permissions']], function () use ($router) {
        $router->group(['prefix' => 'users'], function () use ($router) {
            $router->get('', 'API\Admin\UserController@index');
            $router->post('', 'API\Admin\UserController@store');
            $router->get('{id}', 'API\Admin\UserController@show');
            $router->patch('{id}', 'API\Admin\UserController@update');
            $router->delete('{id}', 'API\Admin\UserController@delete');
        });

        $router->group(['prefix' => 'roles'], function () use ($router) {
            $router->get('', 'API\Admin\RoleController@all');
            $router->post('', 'API\Admin\RoleController@store');
            $router->get('{id}', 'API\Admin\RoleController@show');
            $router->patch('{id}', 'API\Admin\RoleController@update');
            $router->delete('{id}', 'API\Admin\RoleController@delete');
        });

        $router->group(['prefix' => 'permissions'], function () use ($router) {
            $router->get('', 'API\Admin\PermissionController@all');
            $router->post('', 'API\Admin\PermissionController@store');
            $router->get('{id}', 'API\Admin\PermissionController@show');
            $router->patch('{id}', 'API\Admin\PermissionController@update');
            $router->delete('{id}', 'API\Admin\PermissionController@delete');
        });
    });
});
