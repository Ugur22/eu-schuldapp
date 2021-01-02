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
$router->post('/client/docs/debtors','ClientController@postDebtors');
$router->post('/client/docs/debtor','ClientController@postDebtor');
$router->post('/client/docs/others','ClientController@postOthers');
$router->post('/client/docs/other','ClientController@postOther');
$router->post('/client/sign','ClientController@postSign');
/* Search */
$router->post('/client/docs/debts/search','ClientController@postSearchDebt');
$router->post('/client/docs/debtors/search','ClientController@postSearchDebtor');
$router->post('/client/docs/others/search','ClientController@postSearchOther');

/* consultant */
/* get data */
$router->post('/consultant/employers','ConsultantController@employerList');
$router->post('/consultant/companies','ConsultantController@companyList');
$router->post('/consultant/clients','ConsultantController@clientList');
$router->post('/consultant/client','ConsultantController@clientDetails');
$router->post('/consultant/client/create','ConsultantController@createClient');
$router->post('/consultant/client/debts','ConsultantController@clientDebts');
$router->post('/consultant/client/debt/details','ConsultantController@clientDebt');
$router->post('/consultant/client/debt/create','ConsultantController@createClientDebt');
$router->post('/consultant/client/debt/update','ConsultantController@updateClientDebt');
$router->post('/consultant/client/debts/search','ConsultantController@searchClientDebts');


$router->post('/consultant/appointments','ConsultantController@appointmentList');
$router->post('/consultant/appointment','ConsultantController@appointment');
$router->post('/consultant/make-appointment','ConsultantController@makeAppointment');
$router->post('/consultant/doc/forms','ConsultantController@clientFormList');
$router->post('/consultant/doc/form','ConsultantController@clientFormDetails');
$router->post('/consultant/doc/debtors','ConsultantController@clientDeptorDocs');
$router->post('/consultant/doc/debtor','ConsultantController@deptorDocDetails');
$router->post('/consultant/doc/debtor-search','ConsultantController@searchDebtorDocs');
$router->post('/consultant/doc/others','ConsultantController@otherDocList');
$router->post('/consultant/doc/other','ConsultantController@otherDocDetails');
$router->post('/consultant/doc/other-search','ConsultantController@searchOtherDocs');

$router->post('/consultant/client/next-step','ConsultantController@nextClientStatus');
$router->post('/consultant/client/debt/next-steps','ConsultantController@nextDebtStatusList');
$router->post('/consultant/client/debt/next-step','ConsultantController@nextDebtStatus');
$router->post('/consultant/client/templates','ConsultantController@templateList');
$router->post('/consultant/doc/add','ConsultantController@addDocument');
$router->post('/consultant/sign','ConsultantController@toSign');

/* download */
$router->post('/document/html-preview','DownloadController@htmlPreview');
$router->post('/document/pdf-download','DownloadController@formPDF');
$router->post('/document/file-download','DownloadController@clientFile');
$router->post('/document/signatures','DownloadController@checkSignatures');