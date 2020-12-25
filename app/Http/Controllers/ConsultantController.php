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

class ConsultantController extends Controller
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

    public function clientList(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $results = [];
        foreach ($this->jwt->user()->consultant->clients as $key => $client) {
          $results[$key]['id'] = $client->id;
          $results[$key]['firstname'] = $client->firstname;
          $results[$key]['lastname'] = $client->lastname;
          $results[$key]['status'] = $client->status->status;
        }
        
        if(!$results){
            return response()->json(['success' => false, 'message' => 'no_client']);
        }else{
            return response()->json(['success' => true, 'results' => $results]);
        }
    }

    public function clientDetails(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $results = Client::whereId($input['id'])->with('user')->with('location')->first();

        if(!$results){
            return response()->json(['success' => false, 'message' => 'no_client']);
        }else{
            return response()->json(['success' => true, 'results' => $results]);
        }
    }

    public function createClient(Request $request)
    {
      if(!$this->loginFirst($request)){
        return response()->json(['success' => false, 'message' => 'login_error']);
      }
      $this->validate($request, [
        'email'    => 'required|email|max:255',
        'card_id' => 'required',
        'firstname' => 'required',
        'lastname' => 'required',
        'gender' => 'required',
        'birth_date' => 'required',
        'address' => 'required',
        'place_id' => 'required',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
      ]);

      try {
        $input = $request->all();

        $user = new User;
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);

        if($user->save()){
            $client = new Client;
            $client->consultant_id = $this->jwt->user()->id;
            $client->user_id = $user->id;
            $client->initial = isset($input['initial']) ? $input['initial']: '';
            $client->firstname = $input['firstname'];
            $client->lastname = $input['lastname'];
            $client->card_id = $input['card_id'];
            $client->gender = $input['gender'];
            $client->birth_date = $input['birth_date'];
            $client->address = $input['address'];
            $client->place_id = $input['place_id'];
            if($client->save()) {
                return response()->json(['success' => true, 'results' => $user->id]);
            }else{
                return response()->json(['success' => false, 'message' => 'register_failed']);
            }
        }else{
          return response()->json(['success' => false, 'message' => 'register_failed']);
        }

      } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
      }
    }

    public function appointmentList(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $items = $this->jwt->user()->consultant->appointments;
        if($items->count()){
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
        $item = Appointment::with('location')->with('client')->whereId($request->id)->get();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function makeAppointment(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $item = new Appointment;
        $item->event_date = \Carbon\Carbon::parse($input['date'].' '.$input['time']);
        $item->client_id = $input['client_id'];
        $item->location_id = $input['location_id'];
        $item->consultant_id = $this->jwt->user()->id;
        $item->title = $input['title'];
        $item->notes = $input['notes'];
        $item->status = 'pending';

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'add_failed']);
        }
    }

    public function clientDebts(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        $myClients = $this->jwt->user()->consultant->clients()->pluck('id')->toArray();
        $myClient = in_array($input['client_id'], $myClients) ? true: false;
        $items=[];
        if($myClient){
            $debts = Debt::where('client_id', $input['client_id'])->get();
            foreach ($debts as $key => $debt) {
                $items[$key]['id'] = $debt->id;
                $items[$key]['reference_id'] = $debt->reference_id;
                $items[$key]['client'] = $debt->client;
                $items[$key]['debt_amount'] = $debt->debt_amount;
                $items[$key]['debtor'] = $debt->debtor;
            };
        }else{
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function clientDebt(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        $myClients = $this->jwt->user()->consultant->clients()->pluck('id')->toArray();
        $myClient = in_array($input['client_id'], $myClients) ? true: false;
        if($myClient){
            $item = Debt::whereId($input['id'])->with('client')->first();
        }else{
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function searchClientDebts(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        $myClients = $this->jwt->user()->consultant->clients()->pluck('id')->toArray();
        $myClient = in_array($input['client_id'], $myClients) ? true: false;
        $items=[];
        if($myClient){
            $search = trim($input['search']);
            $companies = Company::where('name', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
            $statuses = ClientDebtStatus::where('status', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
            $debts = Debt::where('client_id', $input['client_id'])->where(function($query) use($search, $companies, $statuses) {
                $query->orWhereIn('debtor_id', $companies);
                $query->orWhere('reference_id', 'LIKE', '%'.$search.'%');
                $query->orWhereIn('status_id', $statuses);
                $query->orWhere('notes', 'LIKE', '%'.$search.'%');
            })->get();
            
            foreach ($debts as $key => $debt) {
                $items[$key]['id'] = $debt->id;
                $items[$key]['reference_id'] = $debt->reference_id;
                $items[$key]['client'] = $debt->client;
                $items[$key]['debt_amount'] = $debt->debt_amount;
                $items[$key]['debtor'] = $debt->debtor;
            };
            if(count($items)){
                return response()->json(['success' => true, 'results' => $items]);
            }else{
                return response()->json(['success' => false, 'message' => 'no_debt_found']);
            }
        }else{
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

    }

    /* 

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
    } */
}
