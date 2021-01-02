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
use App\Models\Document;
use App\Models\Consultant;
use App\Models\Appointment;
use App\Models\ClientDebtStatus;
use App\Helpers\TemplateHelpers;
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

    public function formPDF(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
        }else{
            $client_id = $input['client_id'];
        }
        
        $pdf = Document::whereId($input['document_id'])->where('client_id', $client_id)->first();
        $document = $pdf->html->html;
        if($document){
            return PDF::loadHTML($document)->stream($pdf->template->filename.'_'.str_pad($client_id, 4, '0', STR_PAD_LEFT).'.pdf');
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function clientFile(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
        }else{
            $client_id = $input['client_id'];
        }

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNull('template_id')->first();
        
        if($doc){
            return response()->download(storage_path('app/documents/'. str_pad($client_id, 4, '0', STR_PAD_LEFT) .'/'. $doc->file->filename));
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function htmlPreview(Request $request)
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $input = $request->all();
        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
        }else{
            $client_id = $input['client_id'];
        }

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNotNull('template_id')->first();
        
        if($doc){
            /* return response()->json(['success' => true, 'results' => $doc->html->html]); */
            return response()->json(['success' => true, 'results' => '<h1>Hello World</h1>']);
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound']);
        }
    }

    public function checkSignatures()
    {
        if(!$this->loginFirst($request)){
            return response()->json(['success' => false, 'message' => 'login_error']);
        }

        $template = new TemplateHelpers;
        $input = $request->all();
        $doc = Document::find($input['document_id']);
        $signature_required = $template->signatureRequired($doc->html->html);

        if($signature_required){
            return response()->json(['success' => true, 'need_signature_by' => $signature_required]);
        }else{
            return response()->json(['success' => true, 'signature' => 'completed']);
        }
    }
}
