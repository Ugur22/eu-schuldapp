<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Template;
use App\Models\Client;
use App\Helpers\TemplateHelpers;
use Barryvdh\DomPDF\Facade as PDF;

class GeneralController extends Controller
{
    public function getLocations(Request $request)
    {
        $items = Place::select('id', 'name')->where('parent', '!=', 0)->get();
        if($items->count()){
            return response()->json(['success' => true, 'results' => $items]);
        }else{
            return response()->json(['success' => false, 'message' => 'no_location']);
        }
    }

    public function test(){
        $template = Template::find(1);
        $user = Client::find(67);
        $templateHelper = new TemplateHelpers;
        $html = $template->html;

        preg_match_all("/(?:\<\!\-\-(loop)\(([a-z_]+)\)\-\-\>)((.|\n)*?)(?:\<\!\-\-(endloop)\(([a-z_]+)\)\-\-\>)/", $html, $looping);

        $ifs = [
            'children' => $user->children,
            'initial' => $user->partner_lastname
        ];
        $html = $templateHelper->switchDisplay($html, $ifs);
        $html = $templateHelper->looping($html, $ifs);
        $modifieds['place_name'] = $user->location->name;
        $html = $templateHelper->templateStr($html, $user, $modifieds);
        
        return PDF::loadHTML($html)->stream('download.pdf');
    }
}
