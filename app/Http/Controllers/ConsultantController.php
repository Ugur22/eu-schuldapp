<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Debt;
use App\Models\Form;
use App\Models\Place;
use App\Models\Client;
use App\Models\Company;
use App\Models\DocHtml;
use App\Models\DocFile;
use App\Models\Template;
use App\Models\Document;
use App\Models\Consultant;
use App\Models\CompanyType;
use App\Models\Appointment;
use App\Models\ClientStatus;
use App\Models\ClientDebtStatus;
use App\Helpers\TemplateHelpers;
use App\Helpers\ControllerHelpers;
use Barryvdh\DomPDF\Facade as PDF;
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
          $results[$key]['social_security_id'] = $client->social_security_id;
          $results[$key]['firstname'] = $client->firstname;
          $results[$key]['lastname'] = $client->lastname;
          $results[$key]['login'] = $client->user;
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
        $results = Client::whereId($input['id'])->with('children')->with('user')->with('location')->first();

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
        'phonenumber' => 'phonenumber',
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
            $client->phonenumber = $input['phonenumber'];
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

    public function companyList(Request $request){
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $items = Company::where(function($query) {
            $query->whereHas('types', function($q) {
                $q->where('slug', '!=', 'employer');
            });
        })->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_company']);
        }
    }

    public function employerList(Request $request){
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $items = Company::where(function($query) {
            $query->whereHas('types', function($q) {
                $q->where('slug', 'employer');
            });
        })->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_company']);
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
        $input = $request->all();
        $item = Appointment::with('location')->with('client');
        if(isset($input['id'])){
            $item = $item->whereId($input['id'])->first();
        }else{
            if(isset($input['event_date'])){
                $item = $item->where('event_date', '>=', \Carbon\Carbon::parse($input['event_date'])->format('Y-m-d 00:00:00'))->where('event_date', '<=', \Carbon\Carbon::parse($input['event_date'])->format('Y-m-d 23:59:59'));
            }elseif(isset($input['event_from']) && isset($input['event_to'])){
                $item = $item->where('event_date', '>=', \Carbon\Carbon::parse($input['event_from'])->format('Y-m-d 00:00:00'))->where('event_date', '<=', \Carbon\Carbon::parse($input['event_to'])->format('Y-m-d 23:59:59'));
            }
            $item = $item->where('consultant_id', $this->jwt->user()->id)->get();
        }
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

    public function clientDebts(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        $items=[];
        $debts = Debt::where('client_id', $input['client_id'])->get();
        foreach ($debts as $key => $debt) {
            $items[$key]['id'] = $debt->id;
            $items[$key]['reference_id'] = $debt->reference_id;
            $items[$key]['client'] = $debt->client;
            $items[$key]['debt_amount'] = $debt->debt_amount;
            $items[$key]['debtor'] = $debt->debtor;
        }
        
        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function clientDebt(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $item = Debt::whereId($input['id'])->with('client')->with('debtor')->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function createClientDebt(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $this->validate($request, [
            'due_date'    => 'required'
        ]);

        $item = new Debt;
        $item->client_id = $input['client_id'];
        $item->reference_id = $input['reference_id'];
        $item->debtor_id = $input['debtor_id'] ? $input['debtor_id']: null;
        $item->status_id = 1;
        $item->due_date = $input['due_date'];
        $item->preference = $input['preference'];
        $item->terms = $input['terms'];
        $item->debt_amount = $input['debt_amount'];
        $item->total_redeemed = $input['total_redeemed'];
        $item->redeem_per_month = $input['redeem_per_month'];
        $item->total_redemption = $input['total_redemption'];
        $item->notes = $input['notes'];

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'create_failed']);
        }
    }

    public function updateClientDebt(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $this->validate($request, [
            'due_date'    => 'required',
            'debt_amount' => 'required',
        ]);

        $item = Debt::find($input['debt_id']);
        $item->reference_id = $input['reference_id'];
        $item->debtor_id = $input['debtor_id'] ? $input['debtor_id']: null;
        $item->status_id = $input['status_id'];
        $item->due_date = $input['due_date'];
        $item->preference = $input['preference'];
        $item->terms = $input['terms'];
        $item->debt_amount = $input['debt_amount'];
        $item->total_redeemed = $input['total_redeemed'];
        $item->redeem_per_month = $input['redeem_per_month'];
        $item->total_redemption = $input['total_redemption'];
        $item->notes = $input['notes'];

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'update_failed']);
        }
    }

    public function searchClientDebts(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $search = trim($input['search']);
        $companies = Company::where('name', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
        $statuses = ClientDebtStatus::where('status', 'LIKE', '%'.$search.'%')->pluck('id')->toArray();
        $debts = Debt::where('client_id', $input['client_id'])->where(function($query) use($search, $companies, $statuses) {
            $query->orWhereIn('debtor_id', $companies);
            $query->orWhere('reference_id', 'LIKE', '%'.$search.'%');
            $query->orWhereIn('status_id', $statuses);
            $query->orWhere('notes', 'LIKE', '%'.$search.'%');
        })->get();
        
        $items=[];
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

    }

    public function clientFormList(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $items = Document::where('client_id', $input['client_id'])->whereNotNull('template_id')->whereNull('client_debt_id')->orderBy('doc_date_time')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_form']);
        }
    }

    public function clientFormDetails(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = Document::with('client')->with('clientStatus')->with('clientDebt')->whereId($input['id'])->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function clientDeptorDocs(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $items = Document::with('clientDebt')->whereNotNull('client_debt_id')->whereNull('client_status_id')->where('client_id', $input['client_id'])->orderBy('doc_date_time')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function deptorDocDetails(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = Document::with('clientDebt')->with('template')->whereId($input['id'])->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function searchDebtorDocs(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $search = trim($input['search']);
        $debtors = Company::where('name', 'LIKE', '%' . $search . '%')->orWhere('phone', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->pluck('id')->toArray();
        $items = Document::with('clientDebt')->whereNotNull('client_debt_id')->whereNull('client_status_id')->where('client_id', $input['client_id'])->where(function($query) use($search, $debtors) {
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

    public function otherDocList(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $items = Document::has('file')->with('file')->whereNull('template_id')->where('client_id', $input['client_id'])->orderBy('doc_date_time')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }

    public function otherDocDetails(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = Document::whereId($input['id'])->with('file')->with('clientStatus')->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function searchOtherDocs(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $search = trim($input['search']);
        $items = Document::with('file')->whereHas('file')->where('client_id', $input['client_id'])->with('clientStatus')->where('title', 'LIKE', '%'.$search.'%')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }

    public function templateList(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $client = Client::find($input['client_id']);
        $client_status = $client->status->id;
        $client_debt_statuses = $client->debts()->pluck('status_id')->toArray();
        $items = Template::select('id', 'slug', 'filename')->Where('client_status_id', $client_status)->whereNull('client_debt_status_id')->orWhereIn('client_debt_status_id', $client_debt_statuses)->whereNull('client_status_id')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_template']);
        }
    }

    public function nextClientStatus(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $client = Client::find($input['client_id']);
        $client_status = $client->status->sort;
        $next_status = ClientStatus::where('sort', $client_status + 1)->first();
        if($next_status) {
            $client->client_status_id = $next_status->id;
            if($client->save()){
                return response()->json(['success' => true, 'results' => $client->status->status]);
            }else{
                return response()->json(['success' => false, 'message' => 'next_status_failed']);
            }
        }
    }

    public function nextDebtStatusList(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $debt = Debt::find($input['debt_id']);
        $next_statuses = $debt->status->next;
        if($next_statuses->count()){
            return response()->json(['success' => true, 'results' => $next_statuses]);
        }else{
            $next_statuses = ClientDebtStatus::where('sort', '>', $debt->status->sort)->get();
            if($next_statuses->count()){
                return response()->json(['success' => true, 'results' => $next_statuses]);
            }else{
                return response()->json(['success' => false, 'message' => 'end_of_steps']);
            }
        }
    }

    public function nextClientDebtStatus(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $debt = Debt::find($input['debt_id']);
        $debt->status_id = $input['debt_status_id'];
        
        if($debt->save()) {
            return response()->json(['success' => true, 'results' => $debt]);
        }else{
            return response()->json(['success' => false, 'message' => 'next_status_failed']);
        }
    }

    public function addDocument(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $client_id = $input['client_id'];
        $client = Client::find($client_id);
        if(!$helper->myClient($this->jwt->user(), $client_id)) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = new Document;
        $item->doc_date_time = \Carbon\Carbon::now();
        $item->client_id = $client_id;
        $item->client_debt_id = isset($input['debt_id']) ? $input['debt_id']: null;
        if(!isset($input['debt_id'])){
            $item->client_status_id = $client->client_status_id;
        }
        $item->title = $input['title'];
        $item->main = isset($input['main']) && $input['main'] ? $input['main']: null;
        $item->template_id = $input['template_id'] ? $input['template_id']: null;
        if($item->save()){
            if($request->hasFile('file')) {
                $file = new DocFile;
                $file->doc_id = $item->id;
                $filename = $request->file('file')->getClientOriginalName();
                $filetype = $request->file('file')->extension();
                $file->filename = $filename;
                $file->filetype = $filetype;
                if($request->file('file')->move(storage_path('app/documents'), $filename)){
                    $file->save();
                }
            }else{
                $template = Template::find($input['template_id']);
                $templateHelper = new TemplateHelpers;
                $html = $template->html;
                $html = $templateHelper->templateToDoc($template->slug, $client, $template->html);
                $file = new DocHtml;
                $file->html = $html;
                $file->doc_id = $item->id;
                $file->save();
            }
        }

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'add_doc_failed']);
        }
    }

    public function toSign(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $template = new TemplateHelpers;
        $input = $request->all();
        $doc = Document::whereId($input['document_id'])->where('client_id', $input['client_id'])->first();
        
        if($request->hasFile('signature')){
            $signed = $template->uploadSignature($request->file('signature'), $doc, $input['author']);
            if(!$signed){
                return response()->json(['success' => false, 'message' => 'upload_sign_failed']);
            }else{
                return response()->json(['success' => true, 'results' => $signed]);
            }
        }else{
            return response()->json(['success' => false, 'message' => 'no_signature_found']);
        }
    }
}
