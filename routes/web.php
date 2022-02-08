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

$router->get('/', function () use ($router) {
    return response()->json([
        'status' => 200,
        'type' => 'rest',
        'version' => 1
    ]);
});

// Auth
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->get('profile', ['middleware' => 'auth', 'uses' => 'AuthController@profile']);
});

$router->group([
    'middleware' => 'auth'
], function () use ($router) {
    // Users
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('', ['middleware' => 'permission:show users', 'uses' => 'UserController@index']);
        $router->post('', ['middleware' => 'permission:create users', 'uses' => 'UserController@store']);
        $router->get('{id}', ['middleware' => 'permission:show users', 'uses' => 'UserController@show']);
        $router->patch('{id}/update', ['middleware' => 'permission:update users', 'uses' => 'UserController@update']);
        $router->delete('{id}/delete', ['middleware' => 'permission:delete users', 'uses' => 'UserController@delete']);
    });

    // Permissions
    $router->group(['prefix' => 'permissions'], function () use ($router) {
        $router->get('', ['middleware' => 'permission:show permissions', 'uses' => 'PermissionController@index']);
    });

    // Roles
    $router->group(['prefix' => 'roles'], function () use ($router) {
        $router->get('', ['middleware' => 'permission:show roles', 'uses' => 'RoleController@index']);
        $router->post('', ['middleware' => 'permission:create roles', 'uses' => 'RoleController@store']);
        $router->get('{id}', ['middleware' => 'permission:show roles', 'uses' => 'RoleController@show']);
        $router->patch('{id}/update', ['middleware' => 'permission:update roles', 'uses' => 'RoleController@update']);
        $router->delete('{id}/delete', ['middleware' => 'permission:delete roles', 'uses' => 'RoleController@delete']);
    });

    // Measurements
    $router->group(['prefix' => 'measurements'], function () use ($router) {
        $router->get('', 'MeasurementController@index');
        $router->post('', ['middleware' => 'permission:create measurements', 'uses' => 'MeasurementController@store']);
        $router->patch('{id}/update', ['middleware' => 'permission:update measurements', 'uses' => 'MeasurementController@update']);
        $router->delete('{id}/delete', ['middleware' => 'permission:delete measurements', 'uses' => 'MeasurementController@delete']);

        $router->get('detection', ['middleware' => 'permission:show measurements', 'uses' => 'MeasurementController@detection']);
        $router->post('merge', ['middleware' => 'permission:show measurements', 'uses' => 'MeasurementController@merge']);
        $router->post('autosuggestion', ['middleware' => 'permission:merge measurements', 'uses' => 'MeasurementController@autosuggestion']);
    });

    // Export
    $router->get('export', ['middleware' => 'permission:export measurements', 'uses' => 'ExportController@export']);
});
