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

class DownloadController extends Controller
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

    public function formPDF(Request $request, ControllerHelpers $helper)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $client_id = $input['client_id'];

        $pdf = Document::whereId($input['document_id'])->where('client_id', $client_id)->first();
        $document = $pdf->html->html;
        if($document){
            return PDF::loadHTML($document)->stream($pdf->template->filename.'_'.str_pad($client_id, 4, '0', STR_PAD_LEFT).'.pdf');
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function clientFile()
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        $client_id = $input['client_id'];

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNull('template_id')->first();
        
        if($doc){
            return response()->download(storage_path('app/documents/'. str_pad($client_id, 4, '0', STR_PAD_LEFT) .'/'. $doc->file->filename));
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }
}
