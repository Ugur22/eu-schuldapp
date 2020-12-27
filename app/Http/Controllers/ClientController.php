<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Debt;
use App\Models\Form;
use App\Models\Place;
use App\Models\Client;
use App\Models\Company;
use App\Models\Document;
use App\Models\Consultant;
use App\Models\Appointment;
use App\Models\ClientDebtStatus;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;

class ClientController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    private function loginFirst($request) {
        
        $login = $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        return $this->jwt->attempt($request->only('email', 'password'));
    }

    public function clientAccount(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $results = $this->jwt->user()->client;
        
        if(!$results){
            return response()->json(['success' => false, 'message' => 'wrong_credential']);
        }else{
            return response()->json(['success' => true, 'results' => $results]);
        }
    }

    public function postDebts(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $debts = $this->jwt->user()->client->debts;
        $items=[];
        foreach ($debts as $key => $debt) {
            $items[$key]['id'] = $debt->id;
            $items[$key]['reference_id'] = $debt->reference_id;
            $items[$key]['debt_amount'] = $debt->debt_amount;
            $items[$key]['debtor'] = $debt->debtor;
        };
        
        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function postDebt(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $item = Debt::whereId($request->id)->with('debtor')->with('status')->with('client')->get();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postSearchDebt(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $search = trim($request->search);
        $companies = Company::where('name', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
        $statuses = ClientDebtStatus::where('status', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
        
        $debts = $this->jwt->user()->client->debts()->where(function($query) use($search, $companies, $statuses) {
            $query->orWhereIn('debtor_id', $companies);
            $query->orWhere('reference_id', 'LIKE', '%'.$search.'%');
            $query->orWhereIn('status_id', $statuses);
            $query->orWhere('notes', 'LIKE', '%'.$search.'%');
        })->get();

        $items=[];
        foreach ($debts as $key => $debt) {
            $items[$key]['id'] = $debt->id;
            $items[$key]['reference_id'] = $debt->reference_id;
            $items[$key]['debt_amount'] = $debt->debt_amount;
            $items[$key]['debtor'] = $debt->debtor;
        };

        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function appointments(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $appointments = $this->jwt->user()->client->appointments;
        $items=[];
        foreach ($appointments as $key => $appointm) {
            $items[$key]['id'] = $appointm->id;
            $items[$key]['title'] = $appointm->title;
            $items[$key]['notes'] = $appointm->notes;
            $items[$key]['status'] = $appointm->status;
            $items[$key]['event_date'] = $appointm->event_date;
            $items[$key]['client'] = $appointm->client;
            $items[$key]['consultant'] = $appointm->consultant;
            $items[$key]['location'] = $appointm->location;
        };

        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_appointment']);
        }
    }

    public function appointment(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $item = Appointment::with('location')->with('client')->with('consultant')->whereId($request->id)->get();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postForms(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $user = $this->jwt->user()->client;
        
        $items = Document::where('client_id', $user->id)->whereNotNull('template_id')->whereNull('client_debt_id')->orderBy('doc_date_time')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_form']);
        }
    }

    public function postForm(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        $user = $this->jwt->user()->client;
        $item = Document::with('client')->with('clientStatus')->with('clientDebt')->whereId($input['id'])->where('client_id', $user->id)->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postDebtors(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $user = $this->jwt->user()->client;
        $items = Document::with('clientDebt')->whereNotNull('client_debt_id')->whereNull('client_status_id')->where('client_id', $user->id)->orderBy('doc_date_time')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function postDebtor(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $user = $this->jwt->user()->client;
        $input = $request->all();
        $item = Document::with('clientDebt')->with('template')->whereId($input['id'])->where('client_id', $user->id)->first();

        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postSearchDebtor(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $client = $this->jwt->user()->client;
        $input = $request->all();
        $search = trim($input['search']);
        $debtors = Company::where('name', 'LIKE', '%' . $search . '%')->orWhere('phone', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->pluck('id')->toArray();
        $items = Document::with('clientDebt')->whereNotNull('client_debt_id')->whereNull('client_status_id')->where('client_id', $client->id)->where(function($query) use($search, $debtors) {
            $query->orWhere('title', 'LIKE', '%'.$search.'%');
            $query->orWhereHas('clientDebt', function($q) use ($debtors) {
                $q->whereIn('debtor_id', $debtors);
            });
        })->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function postOthers(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $client_id = $this->jwt->user()->client->id;
        $items = Document::has('file')->with('file')->whereNull('template_id')->where('client_id', $client_id)->orderBy('doc_date_time')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }

    public function postOther(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        $item = Document::whereId($input['id'])->with('file')->with('clientStatus')->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postSearchOther(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $client_id = $this->jwt->user()->client->id;
        $input = $request->all();
        $search = trim($input['search']);
        $items = Document::with('file')->whereHas('file')->where('client_id', $client_id)->with('clientStatus')->where('title', 'LIKE', '%'.$search.'%')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }
}
