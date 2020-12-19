<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Debt;
use App\Models\Form;
use App\Models\Calendar;
use App\Models\Document;
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

        try {

            if (!$this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['success' => false, 'message' => 'user_not_found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['success' => false, 'message' => 'token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['success' => false, 'message' => 'token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['success' => false, 'message' => 'token_absent: ' . $e->getMessage()], 500);

        }
    }

    public function postAppointments(Request $request)
    {
        $this->loginFirst($request);
        $items = $this->jwt->user()->appointments;
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_appointment']);
        }
        

        /* return $this->jwt->user(); */
        // return response()->json(compact('token'));
    }

    public function postAppointment(Request $request)
    {
        $this->loginFirst($request);
        $item = Calendar::where('Calendar_ID', $request->id)->first();
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postDebts(Request $request)
    {
        $this->loginFirst($request);
        $items = $this->jwt->user()->debts()->select('ID', 'Incasseerder', 'schuld')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_debt']);
        }
    }

    public function postDebt(Request $request)
    {
        $this->loginFirst($request);
        
        $item = Debt::find($request->id);
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postForms(Request $request)
    {
        $this->loginFirst($request);
        $status = $this->jwt->user()->Status;
        $items = Form::select('ID', 'Filename')->where('Client_Status', $status)->orderBy('Filename')->get();

        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_form']);
        }
    }

    public function postForm(Request $request)
    {
        $this->loginFirst($request);
        
        $item = Form::find($request->id);
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postDeptors(Request $request)
    {
        $this->loginFirst($request);
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
        $this->loginFirst($request);
        
        $item = Document::find($request->id);
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    public function postOthers(Request $request)
    {
        $this->loginFirst($request);
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
        $this->loginFirst($request);
        
        $item = Document::find($request->id);
        if($item){
            return response()->json(['success' => true, 'results' => $item]);
        }else{
            return response()->json(['success' => false, 'message' => 'not_found']);
        }
    }

    /* function index(Request $request)
    {
        $input = $request->all();

        $account = Client::where('bsn', $input['bsn'])->where('email', $input['email'])->first();
        
        return response()->json(['success' => true, 'results' => Auth()::user()]);
        // dd(Hash::make("Test123"));
    } */
}
