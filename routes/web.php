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
$router->get('/client/status','GeneralController@getClientStatus');
$router->get('/company-types','GeneralController@getCompanyTypes');
$router->post('/login','AuthController@postLogin');

$router->group(['middleware' => 'auth:api'], function($router)
{
    /* client */
    $router->post('/token','AuthController@updateToken');
    /* get data */
    $router->get('/client','ClientController@clientAccount');
    $router->get('/client/client/incomes','IncomesController@clientIncomes');
    $router->get('/client/client/outcomes','IncomesController@clientOutcomes');
    $router->get('/client/consultants','ClientController@postConsultants');
    $router->get('/client/appointments','ClientController@appointments');
    $router->get('/client/appointment','ClientController@appointment');
    $router->get('/client/docs/debts','ClientController@postDebts');
    $router->get('/client/docs/debt','ClientController@postDebt');
    $router->get('/client/docs/forms','ClientController@postForms');
    $router->get('/client/docs/form','ClientController@postForm');
    $router->get('/client/docs/debtors','ClientController@postDebtors');
    $router->get('/client/docs/debtor','ClientController@postDebtor');
    $router->get('/client/docs/others','ClientController@postOthers');
    $router->get('/client/docs/other','ClientController@postOther');
    /* Search */
    $router->get('/client/docs/debts/search','ClientController@postSearchDebt');
    $router->get('/client/docs/debtors/search','ClientController@postSearchDebtor');
    $router->get('/client/docs/others/search','ClientController@postSearchOther');
    
    $router->post('/client/sign','ClientController@postSign');

    /* consultant */
    /* get data */
    $router->get('/consultant/company','ConsultantController@getCompany');
    $router->get('/consultant/employers','ConsultantController@employerList');
    $router->get('/consultant/companies','ConsultantController@companyList');
    $router->get('/consultant/companies/all','ConsultantController@allCompanyList');
    $router->get('/consultant/clients','ConsultantController@clientList');
    $router->get('/consultant/client/incomes','IncomesController@clientIncomes');
    $router->get('/consultant/client/outcomes','IncomesController@clientOutcomes');
    $router->get('/consultant/client','ConsultantController@clientDetails');
    $router->get('/consultant/client/delete-child','ConsultantController@deleteChild');
    $router->get('/consultant/client/debts','ConsultantController@clientDebts');
    $router->get('/consultant/client/debt/details','ConsultantController@clientDebt');
    $router->get('/consultant/client/debts/search','ConsultantController@searchClientDebts');

    $router->get('/consultant/appointments','ConsultantController@appointmentList');
    $router->get('/consultant/appointment','ConsultantController@appointment');
    $router->get('/consultant/doc/forms','ConsultantController@clientFormList');
    $router->get('/consultant/doc/form','ConsultantController@clientFormDetails');
    $router->get('/consultant/doc/debtors','ConsultantController@clientDeptorDocs');
    $router->get('/consultant/doc/debtor','ConsultantController@deptorDocDetails');
    $router->get('/consultant/doc/debtor-search','ConsultantController@searchDebtorDocs');
    $router->get('/consultant/doc/others','ConsultantController@otherDocList');
    $router->get('/consultant/doc/other','ConsultantController@otherDocDetails');
    $router->get('/consultant/doc/other-search','ConsultantController@searchOtherDocs');

    $router->get('/consultant/client/debt/next-steps','ConsultantController@nextDebtStatusList');
    $router->get('/consultant/client/debt/next-step','ConsultantController@nextDebtStatus');
    $router->get('/consultant/client/templates','ConsultantController@templateList');

    $router->post('/consultant/doc/add','ConsultantController@addDocument');
    $router->post('/consultant/client/create','ConsultantController@createClient');
    $router->post('/consultant/client/create-complete','ConsultantController@createCompleteClient');
    $router->post('/consultant/client/debt/create','ConsultantController@createClientDebt');
    $router->post('/consultant/client/debt/update','ConsultantController@updateClientDebt');
    $router->post('/consultant/make-appointment','ConsultantController@makeAppointment');
    $router->post('/consultant/company/manage','ConsultantController@manageCompany');
    $router->post('/consultant/client/next-step','ConsultantController@nextClientStatus');
    $router->post('/consultant/client/income/update','IncomesController@updateClientIncomes');
    $router->post('/consultant/client/outcome/update','IncomesController@updateClientOutcomes');
    $router->post('/consultant/sign','ConsultantController@toSign');

    /* download */
    $router->get('/document/file-download','FileController@clientFile');
    $router->get('/document/signatures','FileController@checkSignatures');
    $router->get('/document/html-preview','FileController@htmlPreview');
    $router->get('/document/pdf-download','FileController@formPDF');

});