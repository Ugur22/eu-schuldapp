<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Debt;
use App\Models\Form;
use App\Models\Place;
use App\Models\Child;
use App\Models\Outbox;
use App\Models\Client;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\Company;
use App\Models\DocHtml;
use App\Models\DocFile;
use App\Models\Template;
use App\Models\Document;
use App\Models\Consultant;
use App\Models\CompanyType;
use App\Models\ClientStatus;
use App\Helpers\OutboxHelpers;
use App\Models\ClientDebtStatus;
use App\Helpers\TemplateHelpers;
use App\Helpers\ControllerHelpers;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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

    public function clientList(Request $request)
    {
        $results = [];
        $search = $request->search;
        $status_id = $request->status_id;
        if($search && $status_id){
            $clients = $this->jwt->user()->consultant->clients()->where(function($query) use($search) {
                $query->orWhere('firstname', 'LIKE', '%'.$search.'%');
                $query->orWhere('lastname', 'LIKE', '%'.$search.'%');
                $query->orWhere('social_security_id', 'LIKE', '%'.$search.'%');
            })->where('client_status_id', $status_id)->get();
        }elseif($search && !$status_id){
            $clients = $this->jwt->user()->consultant->clients()->where(function($query) use($search) {
                $query->orWhere('firstname', 'LIKE', '%'.$search.'%');
                $query->orWhere('lastname', 'LIKE', '%'.$search.'%');
                $query->orWhere('social_security_id', 'LIKE', '%'.$search.'%');
            })->get();
        }elseif(!$search && $status_id){
            $clients = $this->jwt->user()->consultant->clients()->where('client_status_id', $status_id)->get();
        }else{
            $clients = $this->jwt->user()->consultant->clients;
        }
        foreach ($clients as $key => $client) {
          $results[$key]['id'] = $client->id;
          $results[$key]['social_security_id'] = $client->social_security_id;
          $results[$key]['firstname'] = $client->firstname;
          $results[$key]['lastname'] = $client->lastname;
          $results[$key]['login'] = $client->user;
          $results[$key]['status'] = $client->status;
          $results[$key]['outboxes'] = $client->outboxes->count();
        }
        
        if(!$results){
            return response()->json(['success' => false, 'message' => 'no_client']);
        }else{
            return response()->json(['success' => true, 'results' => $results]);
        }
    }

    public function clientDetails(Request $request)
    {

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
        $user->role_id = 1;

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

    public function createCompleteClient(Request $request)
    {
        $validation = [
            'firstname' => 'required',
            'lastname' => 'required',
            'gender' => 'required',
            'social_security_id' => 'required|unique:clients',
            'birth_date' => 'required',
            'birth_place' => 'required',
            'nationality' => 'required',
            'id_card_number' => 'required',
            'id_type' => 'required',
            'phonenumber' => 'required',
            'address' => 'required',
            'place_id' => 'required',
        ];
        $input = $request->all();
        if (!$input['id']) {
            $validation['password'] = 'required';
            $validation['email'] = 'required|email|max:255|unique:users';
        }
        if (isset($input['password']) && $input['password']) {
            $validation['confirm_password'] = 'required|same:password';
        }
        $validator = Validator::make($input, $validation);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'messages'=>$validator->errors()->all()]);
        }       

      try {

        if($input['id']){
            $client = Client::find($input['id']);
            $user = User::find($client->user_id);
            if($input['password']) {
                $user->password = Hash::make($input['password']);
                $user->save();
            }
        }else{
            $user = new User;
            $user->email = $input['email'];
            $user->role_id = 1;
            $user->password = Hash::make($input['password']);
            if($user->save()){
                $client = new Client;
                $client->consultant_id = $this->jwt->user()->id;
                $client->user_id = $user->id;
                $client->client_status_id = 1;
            }
        }
        $client->gender = $input['gender'];
        $client->initial = $input['initial'] ? $input['initial']: null;
        $client->firstname = $input['firstname'];
        $client->lastname = $input['lastname'];
        $client->social_security_id = $input['social_security_id'];
        $client->birth_date = \Carbon\Carbon::parse($input['birth_date']);
        $client->birth_place = $input['birth_place'] ? $input['birth_place']: null;
        $client->nationality = $input['nationality'] ? $input['nationality']: null;
        $client->id_type = $input['id_type'];
        $client->id_card_number = $input['id_card_number'];
        $client->marital_status = $input['marital_status'];
        $client->partnership_reg = $input['partnership_reg'] ? $input['partnership_reg']: null;
        $client->address = $input['address'];
        $client->postal_code = $input['postal_code'];
        $client->place_id = $input['place_id'];
        $client->phonenumber = $input['phonenumber'];
        $client->bank_account = $input['bank_account'];
        $client->employer_id = $input['employer_id'];
        $client->authorized_date = \Carbon\Carbon::parse($input['authorized_date']);
        $client->partner_social_security_id = $input['partner_social_security_id'] ? $input['partner_social_security_id']: null;
        $client->partner_initial = $input['partner_initial'] ? $input['partner_initial']: null;
        $client->partner_firstname = $input['partner_firstname'] ? $input['partner_firstname']: null;
        $client->partner_lastname = $input['partner_lastname'] ? $input['partner_lastname']: null;
        $client->partner_gender = $input['partner_gender'] ? $input['partner_gender']: null;
        $client->partner_birth_date = $input['partner_birth_date'] ? \Carbon\Carbon::parse($input['partner_birth_date']): null;
        $client->partner_birth_place = $input['partner_birth_place'] ? $input['partner_birth_place']: null;
        $client->partner_nationality = $input['partner_nationality'] ? $input['partner_nationality']: null;
        $client->partner_id_type = $input['partner_id_type'] ? $input['partner_id_type']: null;
        $client->partner_id_card_number = $input['partner_id_card_number'] ? $input['partner_id_card_number']: null;
        if($client->save()) {
            if (count($input['children'])) {
                $new_children = [];
                foreach ($input['children'] as $key => $child) {
                    if($child['fullname']){
                        if($child['id']){
                            $newkid = Child::find($child['id']);
                        }else{
                            $newkid = new Child;
                            $newkid->client_id = $client->id;
                        }
                        $newkid->fullname = $child['fullname'];
                        $newkid->birth_date = \Carbon\Carbon::parse($child['birth_date']);
                        $newkid->save();
                    }
                }
            }
            return response()->json(['success' => true, 'results' => ['user_id' => $user->id, 'client_id' => $client->id, 'children' => $client->children]]);
        }else{
            return response()->json(['success' => false, 'message' => 'register_failed']);
        }

      } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
      }
    }

    public function deleteChild(Request $request)
    {
        $id = $request->id;
        $child = Child::find($id);
        if($child->delete()){
            return response()->json(['success' => true, 'results' => $id]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_company']);
        }
    }

    public function getCompany(Request $request)
    {
        $company = Company::with('location')->with('types')->find($request->id);
        if($company){
            return response()->json(['success' => true, 'results' => $company]);
        }else{
            return response()->json(['success' => false, 'message' => 'no company found']);
        }
    }

    public function allCompanyList(Request $request)
    {

        $items = Company::with('location')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_company']);
        }
    }

    public function companyList(Request $request){

        $items = Company::where(function($query) {
            $query->whereHas('types', function($q) {
                $q->where('slug', '!=', 'employer');
            });
        })->with('location')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_company']);
        }
    }

    public function employerList(Request $request){

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

    public function manageCompany(Request $request)
    {
        $input = $request->all();
        if($input['id']){
            $company = Company::find($input['id']);
        }else{
            $existed = Company::where('name', $input['name'])->count();
            if($existed){
                return response()->json(['success' => false, 'message' => 'duplicate entry']);
            }
            $company = new Company;
        }

        $company->name = $input['name'];
        $company->address = $input['address'];
        $company->postal_code = $input['postal_code'];
        $company->place_id = $input['place_id'];
        $company->phone = $input['phone'];
        $company->email = $input['email'];
        $company->bank_account = $input['bank_account'];
        
        if($company->save()){
            if($input['id']){
                $company->types()->sync($input['types']);
            }else{
                $company->types()->attach($input['types']);
            }
            return response()->json(['success' => true, 'results' => $company]);
        }else{
            return response()->json(['success' => false, 'message' => 'company failed']);
        }
    }

    public function managePlace(Request $request){
        $input = $request->all();
        if($input['id']){
            $place = Place::find($input['id']);
        }else{
            $existed = Place::where('name', $input['name'])->count();
            if($existed){
                return response()->json(['success' => false, 'message' => 'duplicate entry']);
            }
            $place = new Place;
        }

        $place->name = $input['name'];
        if($place->save()){
            return response()->json(['success' => true, 'results' => $place]);
        }else{
            return response()->json(['success' => false, 'message' => 'failed']);
        }
    }

    public function addPlaces(Request $request){
        $input = $request->all();

        $places = explode(',', $input['places']);
        foreach ($places as $key => $value) {
            $place = new Place;
            $place->name = trim($value);
            $place->save();
        }
        return response()->json(['success' => true, 'message' => 'places added']);
    }

    public function manageIncomeTypes(Request $request){
        $input = $request->all();
        if($input['id']){
            $income = Income::find($input['id']);
        }else{
            $existed = Income::where('name', $input['name'])->count();
            if($existed){
                return response()->json(['success' => false, 'message' => 'duplicate entry']);
            }
            $income = new Income;
            $income->slug = Str::slug($input['name']);
        }

        $income->name = $input['name'];
        if($income->save()){
            return response()->json(['success' => true, 'results' => $place]);
        }else{
            return response()->json(['success' => false, 'message' => 'failed']);
        }
    }

    public function addIncomesTypes(Request $request){
        $input = $request->all();

        $incomes = explode(',', $input['incomes']);
        foreach ($incomes as $key => $value) {
            $income = new Income;
            $income->name = trim($value);
            $income->save();
        }
        return response()->json(['success' => true, 'message' => 'incomes added']);
    }

    public function incomeUp(Request $request){
        $input = $request->all();
        $pos = $input['sort'];
        $upPos = $pos - 1;

        $upper = Income::where('sort', '<', $pos)->orderBy('sort', 'desc')->first();
        if($upper){
            $upper->sort = $pos;
            $upper->save();
        }

        $incomeUp = Income::find($input['id']);
        $incomeUp->sort = $upPos;
        if($incomeUp->save()){
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function incomeDown(Request $request){
        $input = $request->all();
        $pos = $input['sort'];
        $downPos = $pos + 1;

        $downer = Income::where('sort', '>', $pos)->orderBy('sort')->first();
        if($downer){
            $downer->sort = $pos;
            $downer->save();
        }

        $incomeUp = Income::find($input['id']);
        $incomeUp->sort = $downPos;
        if($incomeUp->save()){
            
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function manageOutcomeTypes(Request $request){
        $input = $request->all();
        if($input['id']){
            $outcome = Outcome::find($input['id']);
        }else{
            $existed = Outcome::where('name', $input['name'])->count();
            if($existed){
                return response()->json(['success' => false, 'message' => 'duplicate entry']);
            }
            $outcome = new Outcome;
            $outcome->slug = Str::slug($input['name']);
        }

        $outcome->name = $input['name'];
        if($outcome->save()){
            return response()->json(['success' => true, 'results' => $place]);
        }else{
            return response()->json(['success' => false, 'message' => 'failed']);
        }
    }

    public function addOutcomesTypes(Request $request){
        $input = $request->all();

        $outcomes = explode(',', $input['outcomes']);
        foreach ($outcomes as $key => $value) {
            $outcome = new Outcome;
            $outcome->name = trim($value);
            $outcome->save();
        }
        return response()->json(['success' => true, 'message' => 'incomes added']);
    }

    public function outcomeUp(Request $request){
        $input = $request->all();
        $pos = $input['sort'];
        $upPos = $pos - 1;

        $upper = Outcome::where('sort', '<', $pos)->orderBy('sort', 'desc')->first();
        if($upper){
            $upper->sort = $pos;
            $upper->save();
        }

        $outcomeUp = Outcome::find($input['id']);
        $outcomeUp->sort = $upPos;
        if($outcomeUp->save()){
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function outcomeDown(Request $request){
        $input = $request->all();
        $pos = $input['sort'];
        $downPos = $pos + 1;

        $downer = Outcome::where('sort', '>', $pos)->orderBy('sort')->first();
        if($downer){
            $downer->sort = $pos;
            $downer->save();
        }

        $outcomeUp = Outcome::find($input['id']);
        $outcomeUp->sort = $downPos;
        if($outcomeUp->save()){
            
            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function manageCompanyType(Request $request)
    {
        $input = $request->all();
        if($input['id']){
            $type = CompanyType::find($input['id']);
        }else{
            $existed = CompanyType::where('type', $input['type'])->count();
            if($existed){
                return response()->json(['success' => false, 'message' => 'duplicate entry']);
            }
            $type = new CompanyType;
            $type->slug = Str::slug($input['type']);
        }

        $type->type = $input['type'];
        if($type->save()){
            return response()->json(['success' => true, 'results' => $type]);
        }else{
            return response()->json(['success' => false, 'message' => 'failed']);
        }
    }

    public function clientDebts(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        if($input['client_id'] > 0 && !$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        $items=[];
        $debts = Debt::with('status');
        if (isset($input['status_id'])) {
            $debts = $debts->where('status_id', $input['status_id']);
        }
        if (isset($input['debtor_id'])) {
            $debts = $debts->where('debtor_id', $input['debtor_id']);
        }
        if (isset($input['search'])) {
            $search = $input['search'];
            $client_ids = Client::where(function($query) use($search) {
                $query->orWhere('firstname', 'LIKE', '%'.$search.'%');
                $query->orWhere('lastname', 'LIKE', '%'.$search.'%');
            })->pluck('id')->toArray();
            $debts = $debts->where(function($query) use($search, $client_ids) {
                $query->orWhere('reference_id', 'LIKE', '%'.$search.'%');
                $query->orWhere('notes', 'LIKE', '%'.$search.'%');
                $query->orWhereIn('client_id', $client_ids);
            });
        }
        if($input['client_id']){
            $debts = $debts->where('client_id', $input['client_id'])->get();
        }else{
            $clients = $this->jwt->user()->consultant->clients()->pluck('id')->toArray();
            $debts = $debts->whereIn('client_id', $clients)->get();
        }
        foreach ($debts as $key => $debt) {
            $items[$key]['id'] = $debt->id;
            $items[$key]['client'] = $debt->client;
            $items[$key]['reference_id'] = $debt->reference_id;
            $items[$key]['status'] = $debt->status;
            $items[$key]['terms'] = $debt->terms;
            $items[$key]['percentage'] = $debt->percentage;
            $items[$key]['debt_amount'] = $debt->debt_amount;
            $items[$key]['total_redeemed'] = $debt->total_redeemed;
            $items[$key]['redeem_per_month'] = $debt->redeem_per_month;
            $items[$key]['total_redemption'] = $debt->total_redemption;
            $items[$key]['debtor'] = $debt->debtor;
            $items[$key]['docs'] = $debt->documents;
        }
        
        if(count($items)){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function clientDebt(Request $request, ControllerHelpers $helper)
    {
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $item = Debt::whereId($input['id'])->with('client')->with('status')->with('debtor')->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function createClientDebt(Request $request, ControllerHelpers $helper)
    {
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $this->validate($request, [
            'due_date'    => 'required',
            'debt_amount' => 'required'
        ]);

        $item = new Debt;
        $item->client_id = $input['client_id'];
        $item->reference_id = $input['reference_id'];
        $item->debtor_id = $input['debtor_id'] ? $input['debtor_id']: null;
        $item->status_id = 1;
        $item->due_date = $input['due_date'];
        $item->preference = $input['preference'];
        $item->terms = $input['terms'];
        $item->percentage = $input['percentage'] ? $input['percentage']: 0;
        $item->debt_amount = $input['debt_amount'];
        $item->total_redeemed = $input['total_redeemed'] ? $input['total_redeemed']: 0;
        $item->redeem_per_month = $input['redeem_per_month'] ? $input['redeem_per_month']: 0;
        $item->total_redemption = $input['total_redemption'] ? $input['total_redemption']: 0;
        $item->notes = $input['notes'];

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'create_failed']);
        }
    }

    public function updateClientDebt(Request $request, ControllerHelpers $helper)
    {
        
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $this->validate($request, [
            'due_date'    => 'required',
            'debt_amount' => 'required',
        ]);

        $item = Debt::find($input['id']);
        $item->reference_id = $input['reference_id'];
        $item->debtor_id = $input['debtor_id'] ? $input['debtor_id']: null;
        $item->status_id = $input['status_id'];
        $item->due_date = $input['due_date'];
        $item->preference = $input['preference'];
        $item->percentage = $input['percentage'] ? $input['percentage']: 0;
        $item->terms = $input['terms'];
        $item->debt_amount = $input['debt_amount'];
        $item->total_redeemed = $input['total_redeemed'] ? $input['total_redeemed']: 0;
        $item->redeem_per_month = $input['redeem_per_month'] ? $input['redeem_per_month']: 0;
        $item->total_redemption = $input['total_redemption'] ? $input['total_redemption']: 0;
        $item->notes = $input['notes'];

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'update_failed']);
        }
    }

    public function searchClientDebts(Request $request, ControllerHelpers $helper)
    {
        
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
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $items = Document::where('client_id', $input['client_id'])->whereNotNull('template_id')->whereNull('client_debt_id')->orderBy('template_id');
        if(isset($input['search']) && strlen($input['search']) > 2){
            $items = $items->where('title', 'LIKE', '%'. trim($input['search']) .'%');
        }
        $items = $items->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_form']);
        }
    }

    public function clientFormDetails(Request $request, ControllerHelpers $helper)
    {
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
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $items = Document::with('clientDebt')->whereNotNull('client_debt_id')->whereNull('client_status_id')->where('client_id', $input['client_id'])->orderBy('created_at')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function docsOfDebtors(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $debtors = Debt::where('client_id', $input['client_id'])->pluck('debtor_id')->toArray();
        $debtors = array_values(array_unique($debtors));
        $results = [];
        foreach($debtors as $i=>$debtor_id){
            $debtor = Company::find($debtor_id);
            $results[$i]['debtor'] = $debtor->name;
            $results[$i]['debts'] = Debt::with('documents')->where('client_id', $input['client_id'])->where('debtor_id', $debtor_id)->get();
            /* if($debts->documents->first()){
                foreach ($debts->documents as $k => $doc) {
                    $results[$i]['title'] = $doc->title;
                    $results[$i]['date'] = \Carbon\Carbon::parse($doc->created_at)->format('j M Y, H:i');
                }
            } */
        }

        if(count($results)){
            return response()->json(['success' => true, 'results' => $results]);
        }else{
            return response()->json(['success' => false, 'message' => 'no debtor doc']);
        }
    }

    public function deptorDocDetails(Request $request, ControllerHelpers $helper)
    {
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
        })->orderBy('created_at', 'desc')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debtor']);
        }
    }

    public function otherDocList(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $items = Document::has('file')->with('file');
        if(isset($input['search'])){
            $items = $items->where('title', 'LIKE', '%' . trim($input['search']) . '%');
        }
        $items = $items->whereNull('template_id')->where('client_id', $input['client_id'])->orderBy('created_at', 'desc')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_other_doc']);
        }
    }

    public function otherDocDetails(Request $request, ControllerHelpers $helper)
    {
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

    public function templates(Request $request)
    {
        $input = $request->all();
        $templates = Template::with('clientStep')->with('debtStep')->get();
        
        if($templates->count()){
            return response()->json(['success' => true, 'results' => $templates]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_template']);
        }
    }

    public function templateList(Request $request, ControllerHelpers $helper)
    {

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $client = Client::find($input['client_id']);
        $client_status = $client->status->id;
        $client_debt_statuses = $client->debts()->pluck('status_id')->toArray();
        $items = Template::select('id', 'slug', 'filename');
        if(isset($input['type'])){
            switch ($input['type']) {
                case 'form':
                    $items = $items->where('client_status_id', $client_status)->whereNull('client_debt_status_id');
                    break;
                case 'debtor':
                    $items = $items->whereIn('client_debt_status_id', $client_debt_statuses)->whereNull('client_status_id');
                    break;
                default:
                    $items = $items->where('client_status_id', $client_status)->whereNull('client_debt_status_id')->orWhereIn('client_debt_status_id', $client_debt_statuses)->whereNull('client_status_id');
                    break;
            }
        }
        $items = $items->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_template']);
        }
    }

    public function templateDetails(Request $request)
    {
        $input = $request->all();
        $template = Template::find($input['id']);
        if($template){
            return response()->json(['success' => true, 'results' => $template]);
        }else{
            return response()->json(['success' => false, 'message' => 'no template found']);
        }
    }

    public function nextClientStatus(Request $request, ControllerHelpers $helper)
    {

        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $templateHelper = new TemplateHelpers;
        $client = Client::find($input['client_id']);

        $previousDocs = Document::where('client_id', $client->id)->where('client_status_id', $client->client_status_id)->get();
        if ($previousDocs->count()) {
            foreach ($previousDocs as $key => $doc) {
                if ($templateHelper->signatureRequired($doc->html->html)) {
                    return response()->json(['success' => false, 'message' => 'signature is not complete']);
                }
            }
        }

        $client_status = $client->status->sort;
        $next_status = ClientStatus::where('sort', $client_status + 1)->first();

        if($next_status) {
            $outboxHelper = new OutboxHelpers;
            $client->client_status_id = $next_status->id;
            if($client->save()){
                $templateHelper->generateFormClientStatus($client, $next_status->id);
                $outboxHelper->createOutboxClient($client->id, $next_status->id);
                return response()->json(['success' => true, 'results' => $client->status->status]);
            }else{
                return response()->json(['success' => false, 'message' => 'next_status_failed']);
            }
        }
    }

    public function nextDebtStatus(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        if(!$helper->myClient($this->jwt->user(), $input['client_id'])) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }

        $debt = Debt::find($input['debt_id']);
        $debt->status_id = $input['status_id'];
        if($debt->save()){
            $templateHelper = new TemplateHelpers;
            $outboxHelper = new OutboxHelpers;
            $templateHelper->generateFormDebtStatus($debt->client, $input['status_id'], $debt->id);
            $outboxHelper->createOutboxClient($debt->client_id, null, $input['status_id']);
            return response()->json(['success' => true, 'results' => $debt]);
        }else{
            return response()->json(['success' => false, 'message' => 'change status failed']);
        }
    }

    public function nextDebtStatusList(Request $request, ControllerHelpers $helper)
    {

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

    public function uploadOtherDoc(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        $client_id = $input['client_id'];
        $client = Client::find($client_id);
        if(!$helper->myClient($this->jwt->user(), $client_id)) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = new Document;
        $item->client_id = $client_id;
        $item->client_debt_id = isset($input['debt_id']) ? $input['debt_id']: null;
        if(!isset($input['debt_id'])){
            $item->client_status_id = $client->client_status_id;
        }
        $item->title = $input['title'];
        if($item->save()){
            if($request->hasFile('file')) {
                $file = new DocFile;
                $file->doc_id = $item->id;
                $filename = $request->file('file')->getClientOriginalName();
                $filetype = $request->file('file')->extension();
                $file->filename = $filename;
                $file->filetype = $filetype;
                $file->fileoption = $input['option'];
                if($request->file('file')->move(storage_path('app/documents/'.str_pad($client_id, 4, '0', STR_PAD_LEFT)), $filename)){
                    $file->save();
                }
                $outboxHelper = new OutboxHelpers;
                $outboxHelper->fileToOutbox($item->id, $client_id, $input['option'], (isset($input['debt_id']) ? $input['debt_id']: null));
            }else{
                return response()->json(['success' => false, 'message' => 'no file']);
            }
        }

        if($item->save()){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'add_doc_failed']);
        }
    }

    public function addDocument(Request $request, ControllerHelpers $helper)
    {
        $input = $request->all();
        $client_id = $input['client_id'];
        $client = Client::find($client_id);
        if(!$helper->myClient($this->jwt->user(), $client_id)) {
            return response()->json(['success' => false, 'message' => 'not_client']);
        }
        
        $item = new Document;
        $item->client_id = $client_id;
        $item->client_debt_id = isset($input['debt_id']) ? $input['debt_id']: null;
        if(!isset($input['debt_id'])){
            $item->client_status_id = $client->client_status_id;
        }
        $item->title = $input['title'];
        $item->main = isset($input['main']) && $input['main'] ? $input['main']: null;
        $item->template_id = isset($input['template_id']) && $input['template_id'] ? $input['template_id']: null;
        if($item->save()){
            if($request->hasFile('file')) {
                $file = new DocFile;
                $file->doc_id = $item->id;
                $filename = $request->file('file')->getClientOriginalName();
                $filetype = $request->file('file')->extension();
                $file->filename = $filename;
                $file->filetype = $filetype;
                if($request->file('file')->move(storage_path('app/documents/'.str_pad($client_id, 4, '0', STR_PAD_LEFT)), $filename)){
                    $file->save();
                }
            }else{
                $template = Template::find($input['template_id']);
                $templateHelper = new TemplateHelpers;
                $html = $template->html;
                $doc = Document::find($item->id);
                $html = $templateHelper->templateToDoc($template->slug, $client, $doc, $template->html);
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

        $template = new TemplateHelpers;
        $input = $request->all();
        $doc = Document::whereId($input['document_id'])->where('client_id', $input['client_id'])->first();
        
        if($input['signature']){
            $signed = $template->uploadSignature($input['signature'], $doc, $input['author']);
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
