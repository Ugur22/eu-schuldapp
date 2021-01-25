<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
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
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function formPDF(Request $request)
    {

        $input = $request->all();
        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
        }else{
            $client_id = $input['client_id'];
        }
        
        $pdf = Document::whereId($input['document_id'])->where('client_id', $client_id)->first();
        $document = $pdf->html->html;
        if($document){
          $pdf = PDF::loadHTML($document)->stream($pdf->template->filename.'_'.str_pad($client_id, 4, '0', STR_PAD_LEFT).'.pdf');
          
          return 'data:application/pdf;base64,' . base64_encode($pdf);
        }else{
          return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function clientFile(Request $request)
    {

        $input = $request->all();

        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
        }else{
            $client_id = $input['client_id'];
        }

        $doc = Document::whereId($input['document_id'])->where('client_id', $client_id)->whereNull('template_id')->first();

        if($doc){
            /* return response()->download(storage_path('app/documents/'. str_pad($client_id, 4, '0', STR_PAD_LEFT) .'/'. $doc->file->filename.'.'.$doc->file->filetype)); */
            $path = storage_path('app/documents/'. str_pad($client_id, 4, '0', STR_PAD_LEFT) .'/'. $doc->file->filename);
            $data = file_get_contents($path);
            $mime = File::mimeType($path);
            $base64 = 'data:'. $mime . ';base64,' . base64_encode($data);
            return $base64;
        }else{
            return response()->json(['success' => false, 'message' => 'filenotfound'], 404);
        }
    }

    public function htmlPreview(Request $request)
    {

        $input = $request->all();
        if ($this->jwt->user()->role->slug == 'client'){
            $client_id = $this->jwt->user()->client->id;
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

    public function checkSignatures(Request $request)
    {
        $template = new TemplateHelpers;
        $input = $request->all();
        $doc = Document::find($input['document_id']);
        $signature_required = $template->signatureRequired($doc->html->html);

        if($signature_required){
            return response()->json(['success' => true, 'signature' => 'needed', 'need_signature_by' => $signature_required]);
        }else{
            return response()->json(['success' => true, 'signature' => 'completed']);
        }
    }
}
