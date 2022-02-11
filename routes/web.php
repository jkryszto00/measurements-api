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
        $router->get('', 'UserController@index');
        $router->post('', 'UserController@store');
        $router->get('{id}', 'UserController@show');
        $router->patch('{id}/update', 'UserController@update');
        $router->delete('{id}/delete', 'UserController@delete');
    });

    // Permissions
    $router->group(['prefix' => 'permissions'], function () use ($router) {
        $router->get('', 'PermissionController@index');
    });

    // Roles
    $router->group(['prefix' => 'roles'], function () use ($router) {
        $router->get('', 'RoleController@index');
        $router->post('', 'RoleController@store');
        $router->get('{id}', 'RoleController@show');
        $router->patch('{id}/update', ['middleware' => 'permission:update roles', 'uses' => 'RoleController@update');
        $router->delete('{id}/delete', ['middleware' => 'permission:delete roles', 'uses' => 'RoleController@delete');
    });

    // Measurements
    $router->group(['prefix' => 'measurements'], function () use ($router) {
        $router->get('', 'MeasurementController@index');
        $router->post('', 'MeasurementController@store');
        $router->patch('{id}/update', 'MeasurementController@update');
        $router->delete('{id}/delete', 'MeasurementController@delete');

        $router->get('detection', 'MeasurementController@detection');
        $router->post('merge', 'MeasurementController@merge');
        $router->post('autosuggestion', 'MeasurementController@autosuggestion');
    });

    // Export
    $router->get('export', 'ExportController@export');
});
