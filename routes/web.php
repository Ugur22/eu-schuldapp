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

/* $router->post('/account','ClientController@index'); */
$router->post('/auth/login','AuthController@postLogin');
$router->post('/auth/account','AuthController@postBsn');
$router->post('/client/appointments','ClientController@postAppointments');
$router->post('/client/appointment','ClientController@postAppointment');
$router->post('/client/docs/debts','ClientController@postDebts');
$router->post('/client/docs/debt','ClientController@postDebt');
$router->post('/client/docs/forms','ClientController@postForms');
$router->post('/client/docs/form','ClientController@postForm');
$router->post('/client/docs/debtors','ClientController@postDeptors');
$router->post('/client/docs/debtor','ClientController@postDebtor');
$router->post('/client/docs/others','ClientController@postOthers');
$router->post('/client/docs/other','ClientController@postOther');

// $router->options('/auth/login', ['middleware' => 'cors', '']);


// $router->post('/auth/login',['middleware' => 'cors', 'uses' => 'AuthController@postLogin']);



