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
use Barryvdh\DomPDF\Facade as PDF;

class DownloadController extends Controller
{
    private $myAuth;

    public function __construct(Request $request)
    {
        try {
            $input = $request->all();
            $user = User::where('email', $input['user'])->where('download_token', $input['token'])->first();
            $this->myAuth = $user;
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    public function formPDF(Request $request)
    {

        $input = $request->all();
        if(!$this->myAuth){
            return response()->json(['success' => false, 'message' => 'not allowed'], 404);
        }
        if ($this->myAuth->role->slug == 'client'){
            $client_id = $this->myAuth->client->id;
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

        $input = $request->all();

        if(!$this->myAuth){
            return response()->json(['success' => false, 'message' => 'not allowed'], 404);
        }
        if ($this->myAuth->role->slug == 'client'){
            $client_id = $this->myAuth->client->id;
        }else{
            $client_id = $input['client_id'];
        }

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNull('template_id')->first();

        if($doc){
            return response()->download(storage_path('app/documents/'. str_pad($client_id, 4, '0', STR_PAD_LEFT) .'/'. $doc->file->filename.'.'.$doc->file->filetype));
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function htmlPreview(Request $request)
    {

        $input = $request->all();
        if(!$this->myAuth){
            return response()->json(['success' => false, 'message' => 'not allowed'], 404);
        }
        if ($this->myAuth->role->slug == 'client'){
            $client_id = $this->myAuth->client->id;
        }else{
            $client_id = $input['client_id'];
        }

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNotNull('template_id')->first();
        
        if($doc){
            return $doc->html->html;
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound']);
        }
    }

    public function checkSignatures()
    {

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
