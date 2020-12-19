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
    return $router->app->version();
});

$router->get('index','ClientController@index');
$router->post('/auth/login','AuthController@postLogin');

// $router->options('/auth/login', ['middleware' => 'cors', '']);


// $router->post('/auth/login',['middleware' => 'cors', 'uses' => 'AuthController@postLogin']);



