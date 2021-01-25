<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Place;
use App\Models\Client;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\Company;
use App\Models\Consultant;
use App\Models\ClientIncome;
use App\Models\ClientOutcome;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class IncomesController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;
    private $clientId;
    private $myClient = false;

    public function __construct(JWTAuth $jwt, Request $request)
    {
        $this->jwt = $jwt;
        $role = $this->jwt->user()->role->slug;
        if ($role == 'consultant') {
          $this->clientId = $request->client_id;
          $client = Client::where('consultant_id', $this->jwt->user()->id)->where('id', $this->clientId);
          if($client->count()){
            $this->myClient = true;
          }
        }else{
          $this->clientId = $this->jwt->user()->id;
          $this->myClient = true;
        }
    }

    public function clientIncomes(Request $request)
    {
      if (!$this->myClient) {
        return response()->json(['success' => false, 'message' => 'not client']);
      }
      $results = [];
      $incomes = Income::orderBy('sort')->get();
      $results['client'] = [];
      $results['partner'] = [];
      $total_income_client = 0;
      $total_income_partner = 0;
      foreach ($incomes as $key=>$income) {
        $clientIncome = $income->client()->where('client_id', $this->clientId)->where('client_type', 'client')->first();
        $partnerIncome = $income->client()->where('client_id', $this->clientId)->where('client_type', 'partner')->first();
        $income_client = $clientIncome ? $clientIncome->amount: 0;
        $income_partner = $partnerIncome ? $partnerIncome->amount: 0;
        
        $results['client'][$key]['income_id'] = $income->id;
        $results['client'][$key]['id'] = $clientIncome ? $clientIncome->id: 0;
        $results['client'][$key]['label'] = $income->name;
        $results['client'][$key]['amount'] = (float)$income_client;
        $results['client'][$key]['employer_id'] = $clientIncome ? $clientIncome->employer_id: null;
        $results['client'][$key]['employer_name'] = $clientIncome && $clientIncome->employer ? $clientIncome->employer->name: '-';
        $total_income_client = $income_client + $total_income_client;
        
        $results['partner'][$key]['income_id'] = $income->id;
        $results['partner'][$key]['id'] = $partnerIncome ? $partnerIncome->id: 0;
        $results['partner'][$key]['label'] = $income->name;
        $results['partner'][$key]['amount'] = (float)$income_partner;
        $results['partner'][$key]['employer_id'] = $partnerIncome ? $partnerIncome->employer_id: 'null';
        $total_income_partner = $income_partner + $total_income_partner;
        $results['partner'][$key]['employer_name'] = $partnerIncome && $partnerIncome->employer ? $partnerIncome->employer->name: '-';
      }
      $results['total_income_client'] = $total_income_client;
      $results['total_income_partner'] = $total_income_partner;
      $results['total'] = $total_income_client + $total_income_partner;
      
      if($results){
          return response()->json(['success' => true, 'results' => $results]);
      }else{
          return response()->json(['success' => false, 'message' => 'no income']);
      }
    }

    public function updateClientIncomes(Request $request)
    {
      if (!$this->myClient) {
        return response()->json(['success' => false, 'message' => 'not client']);
      }
      $input = $request->all();
      if($input['id']){
        $clientIncome = ClientIncome::find($input['id']);
      }else{
        $clientIncome = new ClientIncome;
      }

      $clientIncome->client_id = $input['client_id'];
      $clientIncome->client_type = $input['client_type'];
      $clientIncome->employer_id = $input['employer_id'];
      $clientIncome->amount = $input['amount'];
      $clientIncome->income_id = $input['income_id'];

      if($clientIncome->save()){
        return response()->json(['success' => true, 'results' => $clientIncome]);
      }else{
        return response()->json(['success' => false, 'message' => 'income update failed']);
      }
    }

    public function clientOutcomes(Request $request)
    {
      if (!$this->myClient) {
        return response()->json(['success' => false, 'message' => 'not client']);
      }
      $results = [];
      $outcomes = Outcome::orderBy('sort')->get();
      $results['client'] = [];
      $results['partner'] = [];
      $total_outcome_client = 0;
      $total_outcome_partner = 0;
      foreach ($outcomes as $key=>$outcome) {
        $clientOutcome = $outcome->client()->where('client_id', $this->clientId)->where('client_type', 'client')->first();
        $partnerOutcome = $outcome->client()->where('client_id', $this->clientId)->where('client_type', 'partner')->first();
        $outcome_client = $clientOutcome ? $clientOutcome->amount: 0;
        $outcome_partner = $partnerOutcome ? $partnerOutcome->amount: 0;
        
        $results['client'][$key]['outcome_id'] = $outcome->id;
        $results['client'][$key]['id'] = $clientOutcome ? $clientOutcome->id: 0;
        $results['client'][$key]['label'] = $outcome->name;
        $results['client'][$key]['amount'] = (float)$outcome_client;
        $results['client'][$key]['company_id'] = $clientOutcome ? $clientOutcome->company_id: null;
        $results['client'][$key]['client_number'] = $clientOutcome ? $clientOutcome->client_number: null;
        $results['client'][$key]['company_name'] = $clientOutcome && $clientOutcome->company ? $clientOutcome->company->name: '-';
        $total_outcome_client = $outcome_client + $total_outcome_client;
        
        $results['partner'][$key]['outcome_id'] = $outcome->id;
        $results['partner'][$key]['id'] = $partnerOutcome ? $partnerOutcome->id: 0;
        $results['partner'][$key]['label'] = $outcome->name;
        $results['partner'][$key]['amount'] = (float)$outcome_partner;
        $results['partner'][$key]['company_id'] = $partnerOutcome ? $partnerOutcome->company_id: 'null';
        $results['partner'][$key]['client_number'] = $partnerOutcome ? $partnerOutcome->client_number: null;
        $total_outcome_partner = $outcome_partner + $total_outcome_partner;
        $results['partner'][$key]['company_name'] = $partnerOutcome && $partnerOutcome->company ? $partnerOutcome->company->name: '-';
      }
      $results['total_outcome_client'] = $total_outcome_client;
      $results['total_outcome_partner'] = $total_outcome_partner;
      $results['total'] = $total_outcome_client + $total_outcome_partner;
      
      if($results){
          return response()->json(['success' => true, 'results' => $results]);
      }else{
          return response()->json(['success' => false, 'message' => 'no outcome']);
      }
    }

    public function updateClientOutcomes(Request $request)
    {
      if (!$this->myClient) {
        return response()->json(['success' => false, 'message' => 'not client']);
      }
      $input = $request->all();
      if($input['id']){
        $clientOutcome = ClientOutcome::find($input['id']);
      }else{
        $clientOutcome = new ClientOutcome;
      }

      $clientOutcome->client_id = $input['client_id'];
      $clientOutcome->client_type = $input['client_type'];
      $clientOutcome->company_id = $input['company_id'];
      $clientOutcome->client_number = $input['client_number'];
      $clientOutcome->amount = $input['amount'];
      $clientOutcome->outcome_id = $input['outcome_id'];

      if($clientOutcome->save()){
        return response()->json(['success' => true, 'results' => $clientOutcome]);
      }else{
        return response()->json(['success' => false, 'message' => 'income update failed']);
      }
    }
}
