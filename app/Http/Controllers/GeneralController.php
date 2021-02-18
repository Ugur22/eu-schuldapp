<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\Template;
use App\Models\Client;
use App\Models\DocOther;
use App\Models\CompanyType;
use App\Models\Appointment;
use App\Models\ClientStatus;
use App\Models\ClientDebtStatus;
use App\Helpers\TemplateHelpers;
use Barryvdh\DomPDF\Facade as PDF;

class GeneralController extends Controller
{
    public function getLocations(Request $request)
    {
        $items = Place::select('id', 'name')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_location']);
        }
    }
    
    public function getIncomeTypes(Request $request)
    {
        $items = Income::orderBy('sort')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_location']);
        }
    }
    
    public function getOutcomeTypes(Request $request)
    {
        $items = Outcome::orderBy('sort')->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_location']);
        }
    }

    public function getUploadOptions()
    {
        $items = DocOther::all();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no option']);
        }
    }

    public function getClientStatus()
    {
        $items = ClientStatus::all();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_location']);
        }
    }

    public function getDebtStatusses()
    {
        $items = ClientDebtStatus::all();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no statusses']);
        }
    }

    public function getCompanyTypes()
    {
        $types = CompanyType::all();
        if($types->count()){
            return response()->json(['success' => true, 'results' => $types]);
        }else{
            return response()->json(['success' => false, 'message' => 'no types']);
        }
    }

    public function test()
    {
        //
    }
}
