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
        $user = $this->jwt->user();
        $status = $user->Status;
        
        $debts = $this->jwt->user()->debts()->groupBy('Status')->pluck('Status')->toArray();
        $items = Form::select('ID', 'Filename')->where(function ($q) use ($debts, $status) {
            $q->orWhere('Client_Status', $status)->whereNull('Client_Schuld_status');
            $q->orWhereNull('Client_Status')->whereIn('Client_Schuld_status', $debts);
        })->orderBy('Filename')->get();

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
        
        $item = Form::find($request->id);
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postDeptors(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $client_id = $this->jwt->user()->BSN;
        $items = Document::with('clientDebt')->whereNotNull('Client_Schuld_ID')->where('Client', $client_id)->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function postDeptor(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $item = Document::find($request->id);
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
        $client_id = $this->jwt->user()->BSN;
        $search = trim($request->search);
        $items = Document::with('clientDebt')->whereNotNull('Client_Schuld_ID')->where('Client', $client_id)->where(function($query) use($search) {
            $query->orWhere('Filename', 'LIKE', '%'.$search.'%');
            $query->orWhereHas('clientDebt', function($q) use ($search) {
                $q->where(function($q) use ($search) {
                    $q->where('Incasseerder', 'LIKE', '%' . $search . '%');
                });
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
        $client_id = $this->jwt->user()->BSN;
        $items = Document::select('ID', 'DateTime', 'Filename')->whereNull('Client_Schuld_ID')->where('Client', $client_id)->orderBy('Filename')->get();

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
        
        $item = Document::find($request->id);
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
        $client_id = $this->jwt->user()->BSN;
        $search = trim($request->search);
        $items = Document::select('ID', 'DateTime', 'Filename')->whereNull('Client_Schuld_ID')->where('Client', $client_id)->orderBy('Filename')->where(function($query) use($search) {
            $query->orWhere('Filename', 'LIKE', '%'.$search.'%');
        })->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }

    public function postAddDocument(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $item = new Document;
        $item->DateTime = \Carbon\Carbon::now();
        $item->Client = $this->jwt->user()->BSN;
        $item->Client_Schuld_ID = isset($input['schuld_id']) ? $input['schuld_id']: null;
        $item->Filename = $input['filename'];
        $item->data = $input['file'];
        $item->main = $input['main'];

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'add_failed']);
        }
    }
}
