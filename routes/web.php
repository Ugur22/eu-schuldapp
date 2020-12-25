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

$router->get('/test','GeneralController@test');
$router->get('/locations','GeneralController@getLocations');
$router->post('/login','AuthController@postLogin');
/* client */
/* get data */
$router->post('/client','ClientController@clientAccount');
$router->post('/client/consultants','ClientController@postConsultants');
$router->post('/client/appointments','ClientController@appointments');
$router->post('/client/appointment','ClientController@appointment');
$router->post('/client/docs/debts','ClientController@postDebts');
$router->post('/client/docs/debt','ClientController@postDebt');
$router->post('/client/docs/forms','ClientController@postForms');
$router->post('/client/docs/form','ClientController@postForm');
$router->post('/client/docs/debtors','ClientController@postDeptors');
$router->post('/client/docs/debtor','ClientController@postDebtor');
$router->post('/client/docs/others','ClientController@postOthers');
$router->post('/client/docs/other','ClientController@postOther');
/* Add record */
$router->post('/client/docs/add','ClientController@postAddDocument');
/* Search */
$router->post('/client/docs/debts/search','ClientController@postSearchDebt');
$router->post('/client/docs/debtors/search','ClientController@postSearchDebtor');
$router->post('/client/docs/others/search','ClientController@postSearchOther');

/* consultant */
/* get data */
$router->post('/consultant/clients','ConsultantController@clientList');
$router->post('/consultant/client','ConsultantController@clientDetails');
$router->post('/consultant/client/create','ConsultantController@createClient');
$router->post('/consultant/appointments','ConsultantController@appointmentList');
$router->post('/consultant/appointment','ConsultantController@appointment');
$router->post('/consultant/appointment','ConsultantController@appointment');
$router->post('/consultant/client-debts','ConsultantController@clientDebts');
$router->post('/consultant/client-debt-details','ConsultantController@clientDebt');
$router->post('/consultant/client-debts-search','ConsultantController@searchClientDebts');